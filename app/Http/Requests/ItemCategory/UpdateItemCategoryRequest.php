<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the update request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('item_category')->id ?? $this->route('item_category');
            return [
            'name' => 'sometimes|required|string|max:255|unique:item_category,name,' . $categoryId,
            'description' => 'sometimes|required|numeric|min:0|max:100'
        ];
        
    }
}
