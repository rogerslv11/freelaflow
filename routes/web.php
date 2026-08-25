<?php

use App\Livewire\Calendar;
use App\Livewire\ClientShow;
use App\Livewire\Clients;
use App\Livewire\ContractPublic;
use App\Livewire\ContractShow;
use App\Livewire\Contracts;
use App\Livewire\Dashboard;
use App\Livewire\Expenses;
use App\Livewire\Files;
use App\Livewire\Finance;
use App\Livewire\InvoicePublic;
use App\Livewire\InvoiceShow;
use App\Livewire\Invoices;
use App\Livewire\NotificationsIndex;
use App\Livewire\Onboarding;
use App\Livewire\Payments;
use App\Livewire\ProjectShow;
use App\Livewire\Projects;
use App\Livewire\ProposalPublic;
use App\Livewire\ProposalShow;
use App\Livewire\Proposals;
use App\Livewire\Reports;
use App\Livewire\Settings;
use App\Livewire\Tasks;
use App\Livewire\TimeTracking;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {
    // Onboarding (no 'onboarded' gate)
    Route::get('/onboarding', Onboarding::class)->name('onboarding');

    Route::middleware('onboarded')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        Route::get('/clients', Clients::class)->name('clients.index');
        Route::get('/clients/{client}', ClientShow::class)->name('clients.show');

        Route::get('/projects', Projects::class)->name('projects.index');
        Route::get('/projects/{project}', ProjectShow::class)->name('projects.show');

        Route::get('/tasks', Tasks::class)->name('tasks.index');

        Route::get('/proposals', Proposals::class)->name('proposals.index');
        Route::get('/proposals/{proposal}', ProposalShow::class)->name('proposals.show');

        Route::get('/contracts', Contracts::class)->name('contracts.index');
        Route::get('/contracts/{contract}', ContractShow::class)->name('contracts.show');

        Route::get('/invoices', Invoices::class)->name('invoices.index');
        Route::get('/invoices/{invoice}', InvoiceShow::class)->name('invoices.show');

        Route::get('/finance', Finance::class)->name('finance.index');
        Route::get('/payments', Payments::class)->name('payments.index');
        Route::get('/expenses', Expenses::class)->name('expenses.index');
        Route::get('/time', TimeTracking::class)->name('time.index');

        Route::get('/calendar', Calendar::class)->name('calendar.index');

        Route::get('/files', Files::class)->name('files.index');

        Route::get('/reports', Reports::class)->name('reports.index');

        Route::get('/notifications', NotificationsIndex::class)->name('notifications.index');

        Route::get('/settings', Settings::class)->name('settings.index');

        Route::get('/files/{file}/download', function (\App\Models\File $file) {
            abort_if($file->user_id !== auth()->id(), 403);
            if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($file->path)) {
                abort(404);
            }
            return \Illuminate\Support\Facades\Storage::disk('local')->download($file->path, $file->original_name);
        })->name('files.download');
    });
});

// Public (token-protected) pages — no auth required
Route::get('/p/proposal/{proposal}/{token}', ProposalPublic::class)->name('proposals.public');
Route::get('/p/contract/{contract}/{token}', ContractPublic::class)->name('contracts.public');
Route::get('/p/invoice/{invoice}/{token}', InvoicePublic::class)->name('invoices.public');

require __DIR__.'/auth.php';
