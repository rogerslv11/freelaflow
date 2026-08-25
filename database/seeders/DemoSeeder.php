<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\CalendarEvent;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@freelaflow.com'],
            [
                'name' => 'Ana Freelancer',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ],
        );

        $user->update([
            'name' => 'Ana Freelancer',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => 'Ana Studio',
                'currency' => 'BRL',
                'plan' => 'pro',
                'onboarded' => true,
            ],
        );

        // Avoid duplicating demo data on re-runs
        if ($user->clients()->exists()) {
            return;
        }

        $categories = collect(['Software', 'Marketing', 'Equipamentos', 'Transporte', 'Serviços', 'Impostos', 'Outros'])
            ->map(fn ($name) => ExpenseCategory::factory()->create([
                'user_id' => $user->id,
                'name' => $name,
                'color' => '#FF6B00',
            ]));

        $clients = Client::factory()->count(10)->create(['user_id' => $user->id]);

        $projects = collect();
        foreach ($clients->take(8) as $client) {
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $projects->push(Project::factory()->create([
                    'user_id' => $user->id,
                    'client_id' => $client->id,
                ]));
            }
        }
        // ensure at least 15
        while ($projects->count() < 15) {
            $projects->push(Project::factory()->create([
                'user_id' => $user->id,
                'client_id' => $clients->random()->id,
            ]));
        }

        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['todo', 'in_progress', 'review', 'done'];
        foreach ($projects as $project) {
            $n = rand(2, 5);
            for ($i = 0; $i < $n; $i++) {
                Task::factory()->forProject($project)->create([
                    'status' => $statuses[array_rand($statuses)],
                    'priority' => $priorities[array_rand($priorities)],
                ]);
            }
        }
        // standalone tasks
        Task::factory()->count(8)->create([
            'user_id' => $user->id,
            'project_id' => null,
            'client_id' => $clients->random()->id,
        ]);

        foreach ($clients->take(10) as $client) {
            $proposal = Proposal::factory()->create([
                'user_id' => $user->id,
                'client_id' => $client->id,
                'token' => Str::random(40),
            ]);
            $items = \App\Models\ProposalItem::factory()->count(rand(2, 4))->create([
                'proposal_id' => $proposal->id,
            ]);
            $subtotal = $items->sum('total');
            $proposal->update([
                'total' => round($subtotal - $proposal->discount + $proposal->tax, 2),
            ]);
        }

        foreach ($clients->take(10) as $client) {
            $contract = Contract::factory()->create([
                'user_id' => $user->id,
                'client_id' => $client->id,
                'project_id' => $projects->where('client_id', $client->id)->first()?->id,
                'token' => Str::random(40),
            ]);
        }

        foreach ($clients->take(10) as $client) {
            $project = $projects->where('client_id', $client->id)->first();
            for ($i = 0; $i < 2; $i++) {
                $invoice = Invoice::factory()->create([
                    'user_id' => $user->id,
                    'client_id' => $client->id,
                    'project_id' => $project?->id,
                    'number' => 'INV-' . Str::padLeft((string) rand(1000, 9999), 4, '0'),
                    'token' => Str::random(40),
                ]);
                $items = \App\Models\InvoiceItem::factory()->count(rand(2, 4))->create([
                    'invoice_id' => $invoice->id,
                ]);
                $subtotal = $items->sum('total');
                $invoice->update([
                    'total' => round($subtotal - $invoice->discount + $invoice->tax, 2),
                ]);
                if ($invoice->status === 'paid') {
                    Payment::factory()->create([
                        'user_id' => $user->id,
                        'client_id' => $client->id,
                        'project_id' => $project?->id,
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->total,
                        'paid_at' => $invoice->paid_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    ]);
                }
            }
        }

        Expense::factory()->count(20)->create([
            'user_id' => $user->id,
            'category_id' => $categories->random()->id,
            'project_id' => $projects->random()->id,
            'client_id' => $clients->random()->id,
        ]);

        TimeEntry::factory()->count(25)->create([
            'user_id' => $user->id,
            'project_id' => $projects->random()->id,
            'client_id' => $clients->random()->id,
        ]);

        CalendarEvent::factory()->count(15)->create([
            'user_id' => $user->id,
            'client_id' => $clients->random()->id,
            'project_id' => $projects->random()->id,
        ]);

        Notification::factory()->count(12)->create(['user_id' => $user->id]);
        ActivityLog::factory()->count(10)->create(['user_id' => $user->id]);
    }
}
