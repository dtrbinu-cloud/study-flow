<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => [
                'sometimes',
                'required',
                Rule::exists('subjects', 'id')->where('user_id', $this->user()->id),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'study_date' => ['sometimes', 'required', 'date'],
            'start_time' => ['sometimes', 'required', 'date_format:H:i'],
            'end_time' => ['sometimes', 'required', 'date_format:H:i', 'after:start_time'],
            'status' => ['sometimes', Rule::in(['planned', 'in_progress', 'done'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
