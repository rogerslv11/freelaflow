<?php

namespace App\Livewire;

use App\Models\Task;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $userId = Auth::id();
        $service = app(FinancialService::class);

        $summary = $service->summary($userId);
        $revenue = $service->monthlyRevenue($userId);
        $expenses = $service->monthlyExpenses($userId);
        $profit = collect($revenue)->mapWithKeys(function ($v, $k) use ($expenses) {
            return [$k => ($v ?? 0) - ($expenses[$k] ?? 0)];
        });

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'))->values();
        $monthLabels = $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M'))->values();
        $revenueSeries = $months->map(fn ($m) => (float) ($revenue[$m] ?? 0))->values();
        $expenseSeries = $months->map(fn ($m) => (float) ($expenses[$m] ?? 0))->values();
        $profitSeries = $months->map(fn ($m) => round(($profit[$m] ?? 0), 2))->values();

        $colors = ['#FF6B00', '#3B82F6', '#10B981', '#A855F7', '#EC4899', '#F59E0B', '#14B8A6'];

        $revenueByClient = collect($service->revenueByClient($userId))->map(function ($item, $i) use ($colors) {
            $item['color'] = $colors[$i % count($colors)];

            return $item;
        })->toArray();

        $projectsByStatus = $service->projectsByStatus($userId);

        $taskColors = ['todo' => '#6B7280', 'in_progress' => '#3B82F6', 'review' => '#F59E0B', 'done' => '#10B981'];
        $tasksByStatus = collect(Task::STATUSES)->map(function ($s) use ($userId, $taskColors) {
            return [
                'label' => status_label($s),
                'value' => Task::where('user_id', $userId)->where('status', $s)->count(),
                'color' => $taskColors[$s] ?? '#FF6B00',
            ];
        })->filter(fn ($x) => $x['value'] > 0)->values()->all();

        return view('livewire.dashboard', [
            'summary' => $summary,
            'monthLabels' => $monthLabels,
            'revenueSeries' => $revenueSeries,
            'expenseSeries' => $expenseSeries,
            'profitSeries' => $profitSeries,
            'revenueByClient' => $revenueByClient,
            'projectsByStatus' => $projectsByStatus,
            'tasksByStatus' => $tasksByStatus,
            'upcomingInvoices' => $service->upcomingInvoices($userId),
            'recentProjects' => $service->recentProjects($userId),
            'dueTasks' => $service->dueTasks($userId),
            'currency' => Auth::user()->profile?->currency ?? 'BRL',
        ]);
    }
}
