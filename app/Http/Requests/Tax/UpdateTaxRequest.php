<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxRequest extends FormRequest
{
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
        $taxId = $this->route('tax')->id ?? $this->route('tax');

        return [
            'name' => 'sometimes|required|string|max:255|unique:taxes,name,' . $taxId,
            'rate' => 'sometimes|required|numeric|min:0|max:100',
            'type' => 'sometimes|required|string|max:255',
            'effective_date' => 'sometimes|required|date',
            'is_active' => 'sometimes|boolean'
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
            'name.required' => 'Tax name is required',
            'name.unique' => 'Tax name already exists',
            'rate.required' => 'Tax rate is required',
            'rate.numeric' => 'Tax rate must be a number',
            'rate.min' => 'Tax rate cannot be negative',
            'rate.max' => 'Tax rate cannot exceed 100%',
            'type.required' => 'Tax type is required',
            'effective_date.required' => 'Effective date is required',
            'effective_date.date' => 'Effective date must be a valid date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active') && is_string($this->is_active)) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}
