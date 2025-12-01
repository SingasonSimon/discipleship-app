<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->canManageMembers();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $memberId = $this->route('member')?->id;
        $member = $this->route('member');
        
        // Get the user_id to ignore when checking users table uniqueness
        $userIdToIgnore = $member?->user_id;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'regex:/^(\+254|0)[0-9]{9}$/',
                Rule::unique('members', 'phone')->ignore($memberId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('members', 'email')->ignore($memberId),
                Rule::unique('users', 'email')->ignore($userIdToIgnore),
            ],
            'date_of_conversion' => ['required', 'date', 'before_or_equal:today'],
            'preferred_contact' => ['required', 'in:email,call'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'The phone number must be a valid Kenyan phone number (e.g., +254712345678 or 0712345678).',
            'date_of_conversion.before_or_equal' => 'The conversion date cannot be in the future.',
            'preferred_contact.in' => 'Preferred contact must be email or call.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'full name',
            'date_of_conversion' => 'conversion date',
            'preferred_contact' => 'preferred contact method',
        ];
    }
}
