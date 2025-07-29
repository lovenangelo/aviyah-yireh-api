<?php

namespace App\Http\Requests\TrainingMaterial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;

class StoreTrainingMaterialRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'nullable|integer|min:0',
            'expiration_date' => 'required|date|after_or_equal:today',
            'path' => 'required|string|max:2048',
            'thumbnail_path' => 'required|string|max:2048',
            'category_id' => 'required|integer|exists:categories,id',
            'language_id' => 'required|integer|exists:languages,id',
            'is_visible' => 'sometimes|boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->formatErrorResponse(422, 'Upload failed.', $validator->errors()->toArray());
        throw new HttpResponseException($response);
    }
}
