<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MentorshipRequest extends FormRequest
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
        $mentorshipId = $this->route('mentorship')?->id;
        $user = $this->user();

        // For mentors: mentor_id is optional (will be auto-set to current user)
        // For admins: mentor_id is required
        $mentorIdRules = ['nullable'];
        if ($user->isAdmin()) {
            $mentorIdRules = [
                'required',
                'exists:users,id',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'mentor'); // Only users with 'mentor' role can be mentors
                }),
            ];
        }

        return [
            'member_id' => [
                'required',
                'exists:members,id',
                Rule::unique('mentorships', 'member_id')->ignore($mentorshipId)->where(function ($query) {
                    $query->where('status', 'active');
                }),
            ],
            'mentor_id' => $mentorIdRules,
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,completed,paused'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'meeting_frequency' => ['nullable', 'in:weekly,biweekly,monthly'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'member_id.unique' => 'This member already has an active mentorship relationship.',
            'mentor_id.exists' => 'The selected mentor must have the mentor role.',
            'start_date.before_or_equal' => 'Start date cannot be in the future.',
            'end_date.after' => 'End date must be after the start date.',
            'status.in' => 'Status must be active, completed, or paused.',
            'meeting_frequency.in' => 'Meeting frequency must be weekly, biweekly, or monthly.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'member_id' => 'member',
            'mentor_id' => 'mentor',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'meeting_frequency' => 'meeting frequency',
        ];
    }
}
