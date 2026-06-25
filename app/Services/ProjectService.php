<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Models\CompanySetting;
use App\Support\ProjectEstimatedHoursCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProjectService extends BaseService implements ProjectServiceInterface
{
    public function __construct(ProjectRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return parent::create($this->withEstimatedHours($data));
    }

    public function update(int $id, array $data): Model
    {
        return parent::update($id, $this->withEstimatedHours($data));
    }

    private function withEstimatedHours(array $data): array
    {
        $settings = CompanySetting::query()
            ->where('company_id', $data['company_id'])
            ->first();

        $data['estimated_hours'] = ProjectEstimatedHoursCalculator::estimatedHours(
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
            ProjectEstimatedHoursCalculator::resolveHoursPerDay($settings),
        );

        return $data;
    }
}
