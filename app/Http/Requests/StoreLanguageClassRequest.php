<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLanguageClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only admin can create
        return auth()->user() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'professor_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('role', 'professor'))
            ],
            'schedule_time' => 'required|date',
            'student_ids' => 'nullable|array',
            'student_ids.*' => [
                'integer',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('role', 'student'))
            ],
        ];
    }
}
