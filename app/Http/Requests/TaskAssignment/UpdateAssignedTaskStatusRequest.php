<?php

namespace App\Http\Requests\TaskAssignment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignedTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:todo,in_progress,done'],
        ];
    }
}

