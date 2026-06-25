<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectModuleRepositoryInterface;
use App\Contracts\Services\ProjectModuleServiceInterface;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Support\ProjectEstimatedHoursCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProjectModuleService extends BaseService implements ProjectModuleServiceInterface
{
    public function __construct(ProjectModuleRepositoryInterface $repository)
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
        $project = Project::query()->findOrFail($data['project_id']);
        $settings = CompanySetting::query()
            ->where('company_id', $project->company_id)
            ->first();

        $data['estimated_hours'] = ProjectEstimatedHoursCalculator::estimatedHours(
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
            ProjectEstimatedHoursCalculator::resolveHoursPerDay($settings),
        );

        return $data;
    }
}
