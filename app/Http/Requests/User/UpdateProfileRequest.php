<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\UserField;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            UserField::Name->value => 'sometimes|required|string|max:255',
            UserField::FullName->value => 'nullable|string|max:255',
            UserField::Email->value => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            UserField::Address->value => 'nullable|string|max:500',
            UserField::City->value => 'nullable|string|max:100',
            UserField::Country->value => 'nullable|string|max:100',
            UserField::PhoneNumber->value => 'nullable|string|max:20',
            UserField::Role->value => ['sometimes', Rule::in(['admin', 'professor', 'student'])],
        ];
    }
}
