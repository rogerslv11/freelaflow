<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    public function monthlyRevenue(int $userId, int $months = 6): array
    {
        return Payment::where('user_id', $userId)
            ->where('paid_at', '>=', now()->subMonths($months)->startOfMonth())
            ->select(DB::raw('strftime("%Y-%m", paid_at) as month'), DB::raw('sum(amount) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }

    public function monthlyExpenses(int $userId, int $months = 6): array
    {
        return Expense::where('user_id', $userId)
            ->where('incurred_at', '>=', now()->subMonths($months)->startOfMonth())
            ->select(DB::raw('strftime("%Y-%m", incurred_at) as month'), DB::raw('sum(amount) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }

    public function revenueByClient(int $userId, int $limit = 5): array
    {
        return Payment::where('user_id', $userId)
            ->whereNotNull('client_id')
            ->with('client')
            ->select('client_id', DB::raw('sum(amount) as total'))
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->take($limit)
            ->get()
            ->map(fn ($p) => ['label' => $p->client?->name ?? '—', 'value' => (float) $p->total])
            ->toArray();
    }

    public function projectsByStatus(int $userId): array
    {
        $colors = [
            'planning' => '#3B82F6',
            'in_progress' => '#FF6B00',
            'review' => '#A855F7',
            'paused' => '#F59E0B',
            'completed' => '#10B981',
            'cancelled' => '#EF4444',
        ];

        return Project::where('user_id', $userId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn ($p) => [
                'label' => status_label($p->status),
                'value' => $p->total,
                'color' => $colors[$p->status] ?? '#FF6B00',
            ])
            ->toArray();
    }

    public function summary(int $userId): array
    {
        $startOfMonth = now()->startOfMonth();

        $revenueMonth = (float) Payment::where('user_id', $userId)
            ->where('paid_at', '>=', $startOfMonth)->sum('amount');

        $pending = (float) Invoice::where('user_id', $userId)
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->sum('total');

        $expensesMonth = (float) Expense::where('user_id', $userId)
            ->where('incurred_at', '>=', $startOfMonth)->sum('amount');

        $overdue = Invoice::where('user_id', $userId)
            ->whereIn('status', ['sent', 'pending'])
            ->where('due_date', '<', now())
            ->count();

        $activeProjects = Project::where('user_id', $userId)
            ->whereIn('status', ['planning', 'in_progress', 'review', 'paused'])
            ->count();

        $activeClients = Client::where('user_id', $userId)->where('status', 'active')->count();

        return [
            'revenue_month' => $revenueMonth,
            'pending' => $pending,
            'expenses_month' => $expensesMonth,
            'profit_month' => $revenueMonth - $expensesMonth,
            'overdue' => $overdue,
            'active_projects' => $activeProjects,
            'active_clients' => $activeClients,
        ];
    }

    public function upcomingInvoices(int $userId, int $limit = 5)
    {
        return Invoice::with('client')
            ->where('user_id', $userId)
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->orderBy('due_date')
            ->take($limit)
            ->get();
    }

    public function recentProjects(int $userId, int $limit = 5)
    {
        return Project::with('client')
            ->where('user_id', $userId)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function dueTasks(int $userId, int $limit = 5)
    {
        return Task::with('project')
            ->where('user_id', $userId)
            ->whereNotIn('status', ['done'])
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->take($limit)
            ->get();
    }
}
