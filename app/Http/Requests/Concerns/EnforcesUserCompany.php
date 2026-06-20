<?php

namespace App\Http\Requests\Concerns;

use App\Support\CompanyContext;

trait EnforcesUserCompany
{
    protected function prepareForValidation(): void
    {
        if (CompanyContext::canSelectCompany($this->user())) {
            return;
        }

        $companyId = CompanyContext::companyId($this->user());

        if ($companyId) {
            $this->merge(['company_id' => $companyId]);
        }
    }
}
