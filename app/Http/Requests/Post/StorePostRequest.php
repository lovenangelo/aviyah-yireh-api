<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StorePostRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'slug' => 'nullable|string|unique:posts,slug|max:255',
            'published_at' => 'nullable|date',
            'meta_data' => 'nullable|array',
            'meta_data.*' => 'nullable', // Allow any type in meta_data
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'status.in' => 'The status must be one of: draft, published, archived.',
            'slug.unique' => 'This slug is already taken.',
        ];
    }

    /**
     * Handle a failed validation attempt for API.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}

