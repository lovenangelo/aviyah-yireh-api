<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteEventRequest extends FormRequest
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
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:events,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Please provide event IDs to delete.',
            'ids.array' => 'event IDs must be provided as an array.',
            'ids.min' => 'At least one event ID must be provided.',
            'ids.*.exists' => 'One or more event IDs do not exist in the database.',
        ];
    }
}
