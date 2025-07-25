<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkDestroyRolesRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:roles,id']
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'Please provide role IDs to delete.',
            'ids.array' => 'Role IDs must be provided as an array.',
            'ids.min' => 'At least one role ID must be provided.',
            'ids.*.exists' => 'One or more role IDs do not exist in the database.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->formatErrorResponse(422, 'Bulk delete failed.', $validator->errors()->toArray());
        throw new HttpResponseException($response);
    }
}
