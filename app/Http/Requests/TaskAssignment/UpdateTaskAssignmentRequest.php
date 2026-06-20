<?php

namespace App\Http\Requests\TaskAssignment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
