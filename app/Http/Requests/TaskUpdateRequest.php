<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Users can only update deadline and status
        if (auth()->user()->role === 'user') {
            return [
                'deadline' => 'sometimes|required|date|after_or_equal:today',
                'status' => 'sometimes|required|string',
            ];
        }

        // Admins can update all fields
        return [
            'project_id' => 'sometimes|required|exists:projects,id',
            'assigned_user_id' => 'sometimes|required|exists:users,id',
            'title' => 'sometimes|required|string|max:255',
            'deadline' => 'sometimes|required|date|after_or_equal:today',
            'status' => 'sometimes|required|string',
        ];
    }

    /**
     * Return JSON for API requests; default redirect for web.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson() || $this->is('api/*')) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
