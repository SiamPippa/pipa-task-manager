<?php

namespace App\Http\Requests\ProjectModule;

use App\Http\Requests\Concerns\ValidatesProjectModule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectModuleRequest extends FormRequest
{
    use ValidatesProjectModule;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->projectModuleRules($this->route('project_module'));
    }
}
