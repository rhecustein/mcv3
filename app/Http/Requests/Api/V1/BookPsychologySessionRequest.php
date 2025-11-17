<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookPsychologySessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'psychologist_id' => [
                'required',
                'integer',
                'exists:psychologists,id',
            ],
            'session_type' => [
                'required',
                Rule::in(['video', 'audio', 'chat', 'onsite']),
            ],
            'scheduled_at' => [
                'required',
                'date',
                'after:now',
            ],
            'client_concern' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'is_emergency' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'psychologist_id' => 'psychologist',
            'session_type' => 'session type',
            'scheduled_at' => 'scheduled date and time',
            'client_concern' => 'your concern',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'psychologist_id.exists' => 'The selected psychologist does not exist',
            'session_type.in' => 'Invalid session type. Must be video, audio, chat, or onsite',
            'scheduled_at.after' => 'Session must be scheduled for a future date and time',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert is_emergency to boolean if it's a string
        if ($this->has('is_emergency')) {
            $this->merge([
                'is_emergency' => filter_var($this->is_emergency, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
