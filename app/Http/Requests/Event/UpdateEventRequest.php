<?php

namespace App\Http\Requests\Event;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Events;
class UpdateEventRequest extends FormRequest
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
            'title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'location' => ['sometimes', 'nullable', 'string'],
            'start_at' => ['sometimes',  'date'],
            'end_at' => ['sometimes', 'date'],
            'image_url'=>['sometimes','nullable','string'],
        ];
    }
}
