<?php

namespace App\Services;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Contracts\Services\CompanyServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyService extends BaseService implements CompanyServiceInterface
{
    private const LOGO_DIRECTORY = 'companies/logos';

    public function __construct(CompanyRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return parent::create($this->prepareLogoData($data));
    }

    public function update(int $id, array $data): Model
    {
        $company = $this->findOrFail($id);

        return parent::update($id, $this->prepareLogoData($data, $company));
    }

    public function delete(int $id): bool
    {
        $company = $this->findOrFail($id);
        $this->deleteStoredLogo($company->logo);

        return parent::delete($id);
    }

    private function prepareLogoData(array $data, ?Model $company = null): array
    {
        if (! isset($data['logo']) || ! $data['logo'] instanceof UploadedFile) {
            unset($data['logo']);

            return $data;
        }

        if ($company?->logo) {
            $this->deleteStoredLogo($company->logo);
        }

        $data['logo'] = $this->storeLogo($data['logo']);

        return $data;
    }

    private function storeLogo(UploadedFile $logo): string
    {
        return $logo->store(self::LOGO_DIRECTORY, 'public');
    }

    private function deleteStoredLogo(?string $path): void
    {
        if (! $path || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
