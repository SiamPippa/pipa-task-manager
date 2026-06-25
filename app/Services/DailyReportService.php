<?php

namespace App\Services;

use App\Contracts\Repositories\DailyReportRepositoryInterface;
use App\Contracts\Repositories\TimeLogRepositoryInterface;
use App\Contracts\Services\DailyReportServiceInterface;
use App\Models\DailyReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DailyReportService extends BaseService implements DailyReportServiceInterface
{
    public function __construct(
        DailyReportRepositoryInterface $repository,
        private readonly TimeLogRepositoryInterface $timeLogRepository
    ) {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $reportData = $this->extractReportData($data);
        $timeLogData = $this->extractTimeLogData($data);

        return DB::transaction(function () use ($reportData, $timeLogData) {
            $report = parent::create($reportData);
            $this->syncTimeLog($report, $timeLogData);

            return $report->load('timeLog');
        });
    }

    public function update(int $id, array $data): Model
    {
        $reportData = $this->extractReportData($data);
        $timeLogData = $this->extractTimeLogData($data);

        return DB::transaction(function () use ($id, $reportData, $timeLogData) {
            $report = parent::update($id, $reportData);
            $report->load('timeLog');
            $this->syncTimeLog($report, $timeLogData);

            return $report->load('timeLog');
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $report = $this->findOrFail($id, ['timeLog']);
            $report->timeLog?->delete();

            return parent::delete($id);
        });
    }

    private function extractReportData(array $data): array
    {
        return Arr::only($data, [
            'user_id',
            'project_id',
            'project_module_id',
            'task_id',
            'report_date',
            'summary',
            'blocker',
            'tomorrow_plan',
            'progress_percent',
        ]);
    }

    private function extractTimeLogData(array $data): array
    {
        return Arr::only($data, [
            'start_time',
            'end_time',
            'total_minutes',
            'note',
        ]);
    }

    private function syncTimeLog(DailyReport $report, array $timeLogData): void
    {
        $payload = array_merge($timeLogData, [
            'daily_report_id' => $report->id,
            'project_id' => $report->project_id,
            'task_id' => $report->task_id,
            'user_id' => $report->user_id,
        ]);

        if ($report->timeLog) {
            $this->timeLogRepository->update($report->timeLog->id, $payload);

            return;
        }

        $this->timeLogRepository->create($payload);
    }
}
