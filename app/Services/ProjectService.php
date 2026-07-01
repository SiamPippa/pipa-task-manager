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
        $managerIds = $this->extractManagerIds($data);
        $project = parent::create($this->withEstimatedHours($data));
        $this->syncManagers($project, $managerIds);

        return $project;
    }

    public function update(int $id, array $data): Model
    {
        $managerIds = $this->extractManagerIds($data);
        $project = parent::update($id, $this->withEstimatedHours($data));
        $this->syncManagers($project, $managerIds);

        return $project;
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

    private function extractManagerIds(array &$data): array
    {
        $managerIds = array_map('intval', $data['manager_ids'] ?? []);
        unset($data['manager_ids']);

        return array_values(array_unique(array_filter($managerIds)));
    }

    private function syncManagers(Model $project, array $managerIds): void
    {
        $syncData = collect($managerIds)
            ->mapWithKeys(fn (int $userId) => [$userId => [
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]])
            ->all();

        $project->managers()->sync($syncData);
    }
}
