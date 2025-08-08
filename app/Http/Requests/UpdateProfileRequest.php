<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $rules = [
            'category' => ['required', 'in:login'], // Adjust categories if needed
        ];

        if ($this->input('category') === 'login') {
            $rules = array_merge($rules, [
                'currentPassword' => ['required', 'string'],
                'newPassword' => ['required', 'string', 'min:6', 'max:10'],
                'confirmPassword' => ['required', 'same:newPassword'],
            ]);
        }

        return $rules;
    }
}
