<?php

namespace App\Http\Requests\ProjectModule;

use App\Http\Requests\Concerns\ValidatesProjectModule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectModuleRequest extends FormRequest
{
    use ValidatesProjectModule;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->projectModuleRules();
    }
}
