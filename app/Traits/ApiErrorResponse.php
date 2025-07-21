<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ApiErrorResponse
{
    /**
     * Custom validation error code mapping per controller
     * Override this property in your controller to customize
     */
    protected array $validationErrorMap = [];

    /**
     * Custom field constraint mapping per controller
     * Override this property in your controller to customize
     */
    protected array $constraintFieldMap = [];

    /**
     * Sensitive fields to exclude from logs
     * Override this property in your controller to customize
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'api_key',
        'secret',
        'credit_card',
        'card_number',
        'cvv',
        'ssn'
    ];

    /**
     * Handle all types of exceptions and return structured error responses
     */
    protected function handleApiException(\Throwable $e, Request $request, string $context = 'operation'): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return $this->handleValidationError($e, $request);
        }

        if ($e instanceof QueryException) {
            return $this->handleDatabaseError($e, $request, $context);
        }

        return $this->handleGeneralError($e, $request, $context);
    }

    /**
     * Handle validation errors with customizable field mapping
     */
    protected function handleValidationError(ValidationException $e, Request $request): JsonResponse
    {
        $errors = [];

        foreach ($e->errors() as $field => $messages) {
            $errors[] = [
                'field' => $field,
                'code' => $this->getValidationErrorCode($field, $messages[0]),
                'message' => $this->getCustomValidationMessage($field, $messages[0]) ?? $messages[0]
            ];
        }

        return $this->formatErrorResponse(
            code: 'VALIDATION_FAILED',
            message: 'Request failed due to validation errors',
            details: $errors,
            statusCode: 422,
            request: $request
        );
    }

    // Helper function to check if message contains any rule
    private function findMatchingRule($message, $rules)
    {
        foreach ($rules as $rule => $code) {
            if (Str::contains(strtolower($message), $rule)) {
                return $code;
            }
        }
        return null;
    }

    /**
     * Get validation error code with multiple customization options
     */
    protected function getValidationErrorCode(string $field, string $message): string
    {
        return match (true) {
            isset($this->validationErrorMap[$field]) &&
                ($code = $this->findMatchingRule($message, $this->validationErrorMap[$field])) !== null
            => $code,

            isset($this->getGlobalValidationErrorMap()[$field]) &&
                ($code = $this->findMatchingRule($message, $this->getGlobalValidationErrorMap()[$field])) !== null
            => $code,

            ($code = $this->findMatchingRule($message, $this->getCommonValidationRules())) !== null
            => str_replace('{FIELD}', strtoupper($field), $code),

            default => strtoupper($field) . '_VALIDATION_ERROR'
        };
    }

    /**
     * Get custom validation message if defined
     */
    protected function getCustomValidationMessage(string $field, string $originalMessage): ?string
    {
        // Check if controller has custom messages
        if (property_exists($this, 'customValidationMessages')) {
            $customMessages = $this->customValidationMessages;

            // Check field-specific message
            if (isset($customMessages[$field])) {
                if (is_string($customMessages[$field])) {
                    return $customMessages[$field];
                }

                // Check rule-specific message
                foreach ($customMessages[$field] as $rule => $message) {
                    if (Str::contains(strtolower($originalMessage), $rule)) {
                        return $message;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Global validation error map - can be customized via config
     */
    protected function getGlobalValidationErrorMap(): array
    {
        return config('api-errors.validation_map', [
            'name' => [
                'required' => 'NAME_REQUIRED',
                'string' => 'NAME_INVALID_TYPE',
                'max' => 'NAME_TOO_LONG',
                'min' => 'NAME_TOO_SHORT'
            ],
            'email' => [
                'required' => 'EMAIL_REQUIRED',
                'email' => 'EMAIL_INVALID_FORMAT',
                'unique' => 'EMAIL_ALREADY_EXISTS',
                'max' => 'EMAIL_TOO_LONG'
            ],
            'password' => [
                'required' => 'PASSWORD_REQUIRED',
                'confirmed' => 'PASSWORD_CONFIRMATION_MISMATCH',
                'min' => 'PASSWORD_TOO_SHORT'
            ]
        ]);
    }

    /**
     * Common validation rules - can be customized via config
     */
    protected function getCommonValidationRules(): array
    {
        return config('api-errors.common_rules', [
            'required' => '{FIELD}_REQUIRED',
            'string' => '{FIELD}_INVALID_TYPE',
            'integer' => '{FIELD}_INVALID_TYPE',
            'email' => '{FIELD}_INVALID_FORMAT',
            'unique' => '{FIELD}_ALREADY_EXISTS',
            'exists' => '{FIELD}_NOT_FOUND',
            'max' => '{FIELD}_TOO_LONG',
            'min' => '{FIELD}_TOO_SHORT',
            'confirmed' => '{FIELD}_CONFIRMATION_MISMATCH'
        ]);
    }

    /**
     * Handle database errors with customizable field mapping
     */
    protected function handleDatabaseError(QueryException $e, Request $request, string $context = 'operation'): JsonResponse
    {
        switch ($e->getCode()) {
            case '23000':
            case '23505':
                return $this->handleConstraintViolation($e, $request, $context);
            default:
                return $this->handleGeneralError($e, $request, $context);
        }
    }

    /**
     * Handle constraint violations with custom field mapping
     */
    protected function handleConstraintViolation(QueryException $e, Request $request, string $context): JsonResponse
    {
        $field = $this->extractFieldFromConstraintError($e->getMessage());

        // Use custom constraint field mapping if available
        if (isset($this->constraintFieldMap[$field])) {
            $mappedField = $this->constraintFieldMap[$field];
            $field = is_array($mappedField) ? $mappedField['field'] : $mappedField;
        }

        $details = [
            [
                'field' => $field,
                'code' => strtoupper($field) . '_ALREADY_EXISTS',
                'message' => $this->getConstraintErrorMessage($field)
                    ?? "A record with this {$field} already exists"
            ]
        ];

        return $this->formatErrorResponse(
            code: strtoupper($context) . '_CONFLICT',
            message: ucfirst($context) . ' failed due to conflicting data',
            details: $details,
            statusCode: 409,
            request: $request
        );
    }

    /**
     * Get custom constraint error message
     */
    protected function getConstraintErrorMessage(string $field): ?string
    {
        if (property_exists($this, 'customConstraintMessages')) {
            return $this->customConstraintMessages[$field] ?? null;
        }

        return null;
    }

    /**
     * Extract field name from constraint error with custom mapping
     */
    protected function extractFieldFromConstraintError(string $errorMessage): string
    {
        // Check controller-specific constraint patterns first
        if (property_exists($this, 'constraintPatterns')) {
            foreach ($this->constraintPatterns as $pattern => $field) {
                if (preg_match($pattern, $errorMessage, $matches)) {
                    return is_callable($field) ? $field($matches) : $field;
                }
            }
        }

        // Default patterns
        $patterns = [
            "/for key '(\w+)'/i" => 1,
            "/column '(\w+)'/i" => 1,
            "/key '.*\.(\w+)'/i" => 1,
            "/users_(\w+)_unique/i" => 1
        ];

        foreach ($patterns as $pattern => $group) {
            if (preg_match($pattern, $errorMessage, $matches)) {
                return $matches[$group];
            }
        }

        return 'field';
    }

    /**
     * Handle general errors
     */
    protected function handleGeneralError(\Throwable $e, Request $request, string $context = 'operation'): JsonResponse
    {
        Log::error(ucfirst($context) . ' failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $this->sanitizeRequestData($request),
            'user_id' => $request->user()?->id,
            'ip' => $request->ip(),
        ]);

        $errorResponse = [
            'success' => false,
            'error' => [
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'An unexpected error occurred during ' . $context,
            ],
            'timestamp' => now()->toISOString(),
            'request_id' => $this->getRequestId($request)
        ];

        if (config('app.debug')) {
            $errorResponse['error']['debug'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'type' => get_class($e)
            ];
        }

        return response()->json($errorResponse, 500);
    }

    /**
     * Format a structured error response
     */
    protected function formatErrorResponse(
        string $code,
        string $message,
        array $details = [],
        int $statusCode = 400,
        ?Request $request = null
    ): JsonResponse {
        $errorResponse = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'timestamp' => now()->toISOString(),
            'request_id' => $this->getRequestId($request)
        ];

        if (!empty($details)) {
            $errorResponse['error']['details'] = $details;
        }

        return response()->json($errorResponse, $statusCode);
    }

    /**
     * Format a structured success response
     */
    protected function formatSuccessResponse(
        mixed $data = null,
        string $message = 'Operation successful',
        int $statusCode = 200,
        ?Request $request = null
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'request_id' => $this->getRequestId($request)
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get or generate request ID
     */
    protected function getRequestId(?Request $request): string
    {
        if (!$request) {
            return Str::uuid()->toString();
        }

        return $request->header('X-Request-ID')
            ?? $request->header('Request-ID')
            ?? Str::uuid()->toString();
    }

    /**
     * Sanitize request data for logging
     */
    protected function sanitizeRequestData(Request $request): array
    {
        return $request->except($this->sensitiveFields);
    }
}
