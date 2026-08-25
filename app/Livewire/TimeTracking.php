<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TimeTracking extends Component
{
    use SearchableTable;

    public $running = false;

    public $runningSince = null;

    public $description = '';

    public $project_id = '';

    public $client_id = '';

    public $task_id = '';

    public $billable = true;

    public $hourly_rate = '';

    // manual entry
    public $manual_project_id = '';

    public $manual_client_id = '';

    public $manual_description = '';

    public $manual_hours = '';

    public $manual_minutes = '';

    public $manual_billable = true;

    public $manual_rate = '';

    public function getSessionsProperty()
    {
        return TimeEntry::with('project', 'client', 'task')
            ->latest('start_time')
            ->paginate($this->perPage);
    }

    public function getTotalsProperty()
    {
        $entries = TimeEntry::where('user_id', Auth::id())->get();
        $seconds = $entries->sum('duration');
        $billable = $entries->where('billable', true)->sum('duration');

        return [
            'seconds' => $seconds,
            'billable' => $billable,
            'billable_amount' => $entries->sum(fn ($e) => $e->billable_amount),
        ];
    }

    public function start()
    {
        $this->running = true;
        $this->runningSince = now()->timestamp;
    }

    public function stop()
    {
        if (! $this->running) {
            return;
        }
        $duration = max(0, now()->timestamp - $this->runningSince);
        TimeEntry::create([
            'user_id' => Auth::id(),
            'project_id' => $this->project_id ?: null,
            'client_id' => $this->client_id ?: null,
            'task_id' => $this->task_id ?: null,
            'description' => $this->description ?: null,
            'start_time' => now()->subSeconds($duration),
            'end_time' => now(),
            'duration' => $duration,
            'billable' => $this->billable,
            'hourly_rate' => $this->hourly_rate ?: null,
        ]);
        $this->running = false;
        $this->runningSince = null;
        $this->description = '';
        $this->project_id = '';
        $this->client_id = '';
        $this->task_id = '';
        $this->hourly_rate = '';
        $this->dispatch('toast', message: 'Sessão registrada.', type: 'success');
    }

    public function addManual()
    {
        $this->validate([
            'manual_hours' => 'required|numeric|min:0',
            'manual_minutes' => 'nullable|numeric|min:0|max:59',
            'manual_description' => 'nullable',
            'manual_project_id' => 'nullable|exists:projects,id',
            'manual_client_id' => 'nullable|exists:clients,id',
            'manual_rate' => 'nullable|numeric|min:0',
        ]);
        $duration = ((int) $this->manual_hours * 3600) + ((int) ($this->manual_minutes ?? 0) * 60);
        TimeEntry::create([
            'user_id' => Auth::id(),
            'project_id' => $this->manual_project_id ?: null,
            'client_id' => $this->manual_client_id ?: null,
            'description' => $this->manual_description ?: null,
            'start_time' => now()->subSeconds($duration),
            'end_time' => now(),
            'duration' => $duration,
            'billable' => $this->manual_billable,
            'hourly_rate' => $this->manual_rate ?: null,
        ]);
        $this->manual_hours = '';
        $this->manual_minutes = '';
        $this->manual_description = '';
        $this->manual_rate = '';
        $this->dispatch('toast', message: 'Horas adicionadas.', type: 'success');
    }

    public function delete($id)
    {
        TimeEntry::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Sessão excluída.', type: 'success');
    }

    public function render()
    {
        return view('livewire.time-tracking', [
            'sessions' => $this->sessions,
            'totals' => $this->totals,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'tasks' => Task::orderBy('title')->get(),
        ])->title('Controle de horas');
    }
}
