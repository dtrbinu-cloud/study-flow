<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // subject_id harus milik user yang sedang login
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where('user_id', $this->user()->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'study_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['sometimes', Rule::in(['planned', 'in_progress', 'done'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
