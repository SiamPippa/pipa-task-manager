<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TimeLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', TimeLog::class);

        $query = TimeLog::query()
            ->visibleTo($request->user())
            ->with(['project', 'task', 'user'])
            ->latest('start_time');

        return view('time-logs.index', [
            'timeLogs' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', TimeLog::class);

        return view('time-logs.create', $this->formData(new TimeLog));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', TimeLog::class);

        TimeLog::query()->create($this->validated($request));

        return redirect()->route('time-logs.index')->with('success', 'Time log created successfully.');
    }

    public function show(TimeLog $timeLog): View
    {
        $this->authorize('view', $timeLog);

        return view('time-logs.show', compact('timeLog'));
    }

    public function edit(TimeLog $timeLog): View
    {
        $this->authorize('update', $timeLog);

        return view('time-logs.edit', $this->formData($timeLog));
    }

    public function update(Request $request, TimeLog $timeLog): RedirectResponse
    {
        $this->authorize('update', $timeLog);

        $timeLog->update($this->validated($request));

        return redirect()->route('time-logs.index')->with('success', 'Time log updated successfully.');
    }

    public function destroy(TimeLog $timeLog): RedirectResponse
    {
        $this->authorize('delete', $timeLog);
        $timeLog->delete();

        return redirect()->route('time-logs.index')->with('success', 'Time log deleted successfully.');
    }

    private function formData(TimeLog $timeLog): array
    {
        $user = auth()->user();

        return [
            'timeLog' => $timeLog,
            'projects' => Project::query()->visibleTo($user)->orderBy('name')->get(),
            'tasks' => Task::query()->visibleTo($user)->orderBy('title')->get(),
            'users' => User::query()->where('company_id', $user->company_id)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $taskId = $request->integer('task_id');
        $task = Task::query()->with('project')->findOrFail($taskId);

        if (! $task->isVisibleTo($request->user())) {
            abort(403);
        }

        $data = $request->validate([
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('company_id', $task->project->company_id)],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'note' => ['nullable', 'string'],
        ]);

        $data['project_id'] = $task->project_id;
        $data['total_minutes'] = $data['end_time']
            ? Carbon::parse($data['start_time'])->diffInMinutes(Carbon::parse($data['end_time']))
            : 0;

        if (! $request->user()->actingCan(\App\Enums\Permission::TIME_LOGS_MANAGE)) {
            $data['user_id'] = $request->user()->id;
        }

        return $data;
    }
}
