<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use App\Models\Client;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Calendar extends Component
{
    public $month;

    public $year;

    public $editingId = null;

    public $title = '';

    public $description = '';

    public $type = 'event';

    public $starts_at = '';

    public $ends_at = '';

    public $all_day = false;

    public $client_id = '';

    public $project_id = '';

    public $selectedDate = null;

    protected function rules()
    {
        return [
            'title' => 'required|min:2|max:160',
            'description' => 'nullable',
            'type' => 'required|in:'.implode(',', CalendarEvent::TYPES),
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'all_day' => 'boolean',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
        ];
    }

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function goToCurrentMonth()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function getUpcomingEventsProperty()
    {
        return CalendarEvent::where('user_id', Auth::id())
            ->whereDate('start_at', '>=', now()->startOfDay())
            ->orderBy('start_at')
            ->with(['client', 'project'])
            ->take(6)
            ->get();
    }

    public function getGridProperty()
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $firstDay = $start->dayOfWeekIso; // 1 (Mon) .. 7 (Sun)
        $days = collect();
        for ($i = 1; $i < $firstDay; $i++) {
            $days->push(null);
        }
        for ($d = 1; $d <= $start->daysInMonth; $d++) {
            $days->push(Carbon::create($this->year, $this->month, $d));
        }
        while ($days->count() % 7 !== 0) {
            $days->push(null);
        }

        return $days->chunk(7);
    }

    public function getEventsByDayProperty()
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return CalendarEvent::where('user_id', Auth::id())
            ->whereBetween('start_at', [$start, $end])
            ->orderBy('starts_at')
            ->with(['client', 'project'])
            ->get()
            ->groupBy(fn ($e) => $e->start_at->format('Y-m-d'));
    }

    public function getEventsForDay($date)
    {
        return CalendarEvent::where('user_id', Auth::id())
            ->whereDate('starts_at', $date->format('Y-m-d'))
            ->orderBy('starts_at')
            ->get();
    }

    public function create($date = null)
    {
        $this->resetForm();
        $this->selectedDate = $date ?? Carbon::create($this->year, $this->month, 1)->format('Y-m-d');
        $this->starts_at = $this->selectedDate.' 09:00';
        $this->dispatch('modal-show', id: 'event-form');
    }

    public function edit($id)
    {
        $e = CalendarEvent::findOrFail($id);
        $this->editingId = $e->id;
        foreach (['title', 'description', 'type', 'starts_at', 'ends_at', 'client_id', 'project_id'] as $f) {
            $this->$f = $e->$f;
        }
        $this->all_day = $e->all_day;
        $this->dispatch('modal-show', id: 'event-form');
    }

    public function save()
    {
        $data = $this->validate();
        if ($this->editingId) {
            CalendarEvent::findOrFail($this->editingId)->update($data);
            $msg = 'Evento atualizado.';
        } else {
            CalendarEvent::create(array_merge($data, ['user_id' => Auth::id()]));
            $msg = 'Evento criado.';
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function delete($id)
    {
        CalendarEvent::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Evento excluído.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['title', 'description', 'starts_at', 'ends_at', 'client_id', 'project_id'] as $f) {
            $this->$f = '';
        }
        $this->type = 'event';
        $this->all_day = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.calendar', [
            'grid' => $this->grid,
            'eventsByDay' => $this->eventsByDay,
            'upcomingEvents' => $this->upcomingEvents,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'types' => CalendarEvent::TYPES,
            'monthLabel' => Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y'),
        ])->title('Agenda');
    }
}
