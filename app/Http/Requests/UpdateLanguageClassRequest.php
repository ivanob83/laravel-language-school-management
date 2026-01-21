<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLanguageClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only admin can update
        return auth()->user() !== null && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'professor_id' => [
                'sometimes',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('role', 'professor'))
            ],
            'schedule_time' => 'sometimes|date',
            'student_ids' => 'nullable|array',
            'student_ids.*' => [
                'integer',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('role', 'student'))
            ],
            'status' => ['sometimes', Rule::in(['scheduled', 'completed'])],
        ];
    }
}
