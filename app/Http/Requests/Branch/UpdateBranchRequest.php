<?php

namespace App\Http\Requests\Branch;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBranchRequest extends FormRequest
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
     * Get the validation rules that apply to the update request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $branchId = $this->route('branch')->id ?? $this->route('branch');

        return [
            'name' => 'sometimes|required|string|max:255|unique:branches,name,'.$branchId,
        ];

    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Job type name is required',
            'name.unique' => 'Job type name already exists',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void {}

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
