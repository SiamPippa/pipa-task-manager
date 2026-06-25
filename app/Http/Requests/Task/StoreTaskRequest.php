<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\Concerns\ValidatesTask;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    use ValidatesTask;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->taskRules();
    }
}
