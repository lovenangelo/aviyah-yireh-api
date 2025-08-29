<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = User::$rules;
        $rules['role_id'] = 'nullable|exists:roles,id';

        // Validate email uniqueness for updates (except for the current user)
        $userId = $this->route('user') ?? auth()->id();
        $rules['email'] = 'required|string|max:255|email|unique:users,email,'.$userId;

        unset($rules['password']);

        return $rules;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->formatErrorResponse(
                code: 'INVALID_REQUEST',
                message: 'Validation failed',
                statusCode: 422,
                details: $validator->errors()->toArray()
            )
        );
    }

    /**
     * Determine if the request is expecting a JSON response.
     */
    public function expectsJson(): bool
    {
        return true;
    }

    /**
     * Determine if the current request is asking for JSON.
     */
    public function wantsJson(): bool
    {
        return true;
    }
}
