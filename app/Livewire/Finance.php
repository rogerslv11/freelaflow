<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Payment;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Finance extends Component
{
    public function render()
    {
        $userId = Auth::id();
        $service = app(FinancialService::class);
        $summary = $service->summary($userId);

        $revenue = $service->monthlyRevenue($userId);
        $expenses = $service->monthlyExpenses($userId);
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'))->values();
        $monthLabels = $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M'))->values();
        $revenueSeries = $months->map(fn ($m) => (float) ($revenue[$m] ?? 0))->values();
        $expenseSeries = $months->map(fn ($m) => (float) ($expenses[$m] ?? 0))->values();

        $paymentsTotal = Payment::where('user_id', $userId)->sum('amount');
        $expensesTotal = Expense::where('user_id', $userId)->sum('amount');

        return view('livewire.finance', [
            'summary' => $summary,
            'monthLabels' => $monthLabels,
            'revenueSeries' => $revenueSeries,
            'expenseSeries' => $expenseSeries,
            'paymentsTotal' => $paymentsTotal,
            'expensesTotal' => $expensesTotal,
            'recentPayments' => Payment::with('client')->where('user_id', $userId)->latest('paid_at')->take(6)->get(),
            'recentExpenses' => Expense::with('category')->where('user_id', $userId)->latest('incurred_at')->take(6)->get(),
        ])->title('Financeiro');
    }
}
