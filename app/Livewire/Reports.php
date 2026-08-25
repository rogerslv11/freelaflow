<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Reports extends Component
{
    public $period = 'month';

    public $startDate = '';

    public $endDate = '';

    public function updatedPeriod($value)
    {
        $this->applyPreset($value);
    }

    public function mount()
    {
        $this->applyPreset('month');
    }

    protected function applyPreset($preset)
    {
        $now = now();
        match ($preset) {
            'today' => [$this->startDate = $now->copy()->startOfDay()->format('Y-m-d'), $this->endDate = $now->copy()->endOfDay()->format('Y-m-d')],
            'week' => [$this->startDate = $now->copy()->startOfWeek()->format('Y-m-d'), $this->endDate = $now->copy()->endOfWeek()->format('Y-m-d')],
            'month' => [$this->startDate = $now->copy()->startOfMonth()->format('Y-m-d'), $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d')],
            'year' => [$this->startDate = $now->copy()->startOfYear()->format('Y-m-d'), $this->endDate = $now->copy()->endOfYear()->format('Y-m-d')],
            'custom' => null,
            default => [$this->startDate = $now->copy()->startOfMonth()->format('Y-m-d'), $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d')],
        };
    }

    protected function range()
    {
        return [
            'start' => $this->startDate.' 00:00:00',
            'end' => $this->endDate.' 23:59:59',
        ];
    }

    public function getStatsProperty()
    {
        $r = $this->range();
        $userId = Auth::id();

        $revenue = (float) Payment::where('user_id', $userId)->whereBetween('paid_at', [$r['start'], $r['end']])->sum('amount');
        $expenses = (float) Expense::where('user_id', $userId)->whereBetween('incurred_at', [$r['start'], $r['end']])->sum('amount');
        $invoices = Invoice::where('user_id', $userId)->whereBetween('created_at', [$r['start'], $r['end']])->count();
        $paidInvoices = Invoice::where('user_id', $userId)->whereBetween('paid_at', [$r['start'], $r['end']])->count();
        $newClients = Client::where('user_id', $userId)->whereBetween('created_at', [$r['start'], $r['end']])->count();
        $activeProjects = Project::where('user_id', $userId)->whereIn('status', ['planning', 'in_progress', 'review', 'paused'])->count();
        $completedTasks = Task::where('user_id', $userId)->where('status', 'done')->whereBetween('updated_at', [$r['start'], $r['end']])->count();
        $seconds = (int) TimeEntry::where('user_id', $userId)->whereBetween('start_time', [$r['start'], $r['end']])->sum('duration');

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $revenue - $expenses,
            'margin' => $revenue > 0 ? round(($revenue - $expenses) / $revenue * 100, 1) : 0,
            'invoices' => $invoices,
            'paid_invoices' => $paidInvoices,
            'new_clients' => $newClients,
            'active_projects' => $activeProjects,
            'completed_tasks' => $completedTasks,
            'hours' => $seconds,
        ];
    }

    public function getTrendProperty()
    {
        $userId = Auth::id();
        $labels = [];
        $revenue = [];
        $expenses = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M');
            $start = $month->copy()->startOfMonth()->format('Y-m-d 00:00:00');
            $end = $month->copy()->endOfMonth()->format('Y-m-d 23:59:59');
            $revenue[] = round((float) Payment::where('user_id', $userId)->whereBetween('paid_at', [$start, $end])->sum('amount'), 2);
            $expenses[] = round((float) Expense::where('user_id', $userId)->whereBetween('incurred_at', [$start, $end])->sum('amount'), 2);
        }

        return [
            'labels' => $labels,
            'series' => [
                ['label' => 'Receita', 'values' => $revenue, 'color' => '#FF6B00'],
                ['label' => 'Despesas', 'values' => $expenses, 'color' => '#EF4444'],
            ],
        ];
    }

    public function getInvoiceStatusProperty()
    {
        $r = $this->range();
        $userId = Auth::id();
        $colors = [
            'paid' => '#22C55E', 'sent' => '#3B82F6', 'overdue' => '#EF4444',
            'draft' => '#6B7280', 'cancelled' => '#A855F7', 'pending' => '#F59E0B',
        ];

        return collect(Invoice::STATUSES)->map(function ($s) use ($userId, $r, $colors) {
            return [
                'label' => status_label($s),
                'value' => Invoice::where('user_id', $userId)->where('status', $s)->whereBetween('created_at', [$r['start'], $r['end']])->count(),
                'color' => $colors[$s] ?? '#FF6B00',
            ];
        })->filter(fn ($s) => $s['value'] > 0)->values()->all();
    }

    public function getTopClientsProperty()
    {
        $r = $this->range();
        $userId = Auth::id();

        return Client::where('user_id', $userId)
            ->withSum(['payments as revenue' => fn ($q) => $q->whereBetween('paid_at', [$r['start'], $r['end']])], 'amount')
            ->orderByDesc('revenue')
            ->take(5)
            ->get()
            ->map(fn ($c) => ['name' => $c->name, 'value' => (float) ($c->revenue ?? 0)])
            ->filter(fn ($c) => $c['value'] > 0)
            ->values()
            ->all();
    }

    public function getExpenseByCategoryProperty()
    {
        $r = $this->range();
        $userId = Auth::id();
        $colors = ['#FF6B00', '#3B82F6', '#10B981', '#A855F7', '#EC4899', '#F59E0B', '#14B8A6'];

        return Expense::where('user_id', $userId)
            ->whereBetween('incurred_at', [$r['start'], $r['end']])
            ->with('category')
            ->get()
            ->groupBy(fn ($e) => $e->category?->name ?? 'Outros')
            ->map(fn ($g) => $g->sum('amount'))
            ->sortDesc()
            ->map(function ($value, $label) use (&$colors) {
                static $i = 0;
                $color = $colors[$i % count($colors)];
                $i++;

                return ['label' => $label, 'value' => round((float) $value, 2), 'color' => $color];
            })
            ->values()
            ->all();
    }

    public function getTasksByStatusProperty()
    {
        $userId = Auth::id();
        $colors = ['todo' => '#6B7280', 'in_progress' => '#3B82F6', 'review' => '#F59E0B', 'done' => '#10B981'];

        return collect(Task::STATUSES)->map(function ($s) use ($userId, $colors) {
            return [
                'label' => status_label($s),
                'value' => Task::where('user_id', $userId)->where('status', $s)->count(),
                'color' => $colors[$s] ?? '#FF6B00',
            ];
        })->filter(fn ($x) => $x['value'] > 0)->values()->all();
    }

    public function getHoursTrendProperty()
    {
        $userId = Auth::id();
        $labels = [];
        $values = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M');
            $start = $month->copy()->startOfMonth()->format('Y-m-d 00:00:00');
            $end = $month->copy()->endOfMonth()->format('Y-m-d 23:59:59');
            $seconds = (int) TimeEntry::where('user_id', $userId)->whereBetween('start_time', [$start, $end])->sum('duration');
            $values[] = round($seconds / 3600, 1);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function exportCsv()
    {
        $stats = $this->stats;
        $filename = 'relatorio_'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($stats) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Métrica', 'Valor']);
            fputcsv($out, ['Receita', number_format($stats['revenue'], 2, ',', '')]);
            fputcsv($out, ['Despesas', number_format($stats['expenses'], 2, ',', '')]);
            fputcsv($out, ['Lucro', number_format($stats['profit'], 2, ',', '')]);
            fputcsv($out, ['Faturas emitidas', $stats['invoices']]);
            fputcsv($out, ['Faturas pagas', $stats['paid_invoices']]);
            fputcsv($out, ['Novos clientes', $stats['new_clients']]);
            fputcsv($out, ['Projetos ativos', $stats['active_projects']]);
            fputcsv($out, ['Tarefas concluídas', $stats['completed_tasks']]);
            fputcsv($out, ['Horas registradas', round($stats['hours'] / 3600, 2)]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    public function render()
    {
        return view('livewire.reports', [
            'stats' => $this->stats,
            'trend' => $this->trend,
            'invoiceStatus' => $this->invoiceStatus,
            'topClients' => $this->topClients,
            'expenseByCategory' => $this->expenseByCategory,
            'tasksByStatus' => $this->tasksByStatus,
            'hoursTrend' => $this->hoursTrend,
        ])->title('Relatórios');
    }
}
