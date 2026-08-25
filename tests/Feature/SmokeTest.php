<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function authUser(): User
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id, 'onboarded' => true]);

        return $user;
    }

    public function test_authenticated_pages_render(): void
    {
        $user = $this->authUser();

        $pages = [
            'dashboard', 'clients', 'projects', 'tasks', 'proposals',
            'contracts', 'invoices', 'payments', 'expenses', 'finance',
            'time', 'calendar', 'files', 'reports', 'notifications', 'settings',
        ];

        foreach ($pages as $page) {
            $this->actingAs($user)
                ->get("/$page")
                ->assertStatus(200, "Failed on /$page");
        }

        $this->assertTrue(true);
    }

    public function test_public_token_pages_render(): void
    {
        $user = $this->authUser();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $invoice = Invoice::factory()->create(['user_id' => $user->id, 'client_id' => $client->id]);
        $proposal = Proposal::factory()->create(['user_id' => $user->id, 'client_id' => $client->id]);
        $contract = Contract::factory()->create(['user_id' => $user->id, 'client_id' => $client->id]);

        $this->get("/p/invoice/{$invoice->id}/{$invoice->token}")->assertStatus(200);
        $this->get("/p/proposal/{$proposal->id}/{$proposal->token}")->assertStatus(200);
        $this->get("/p/contract/{$contract->id}/{$contract->token}")->assertStatus(200);
    }

    public function test_demo_login_action(): void
    {
        Livewire::test('pages.auth.login')
            ->call('demoLogin')
            ->assertRedirect('dashboard');

        $this->assertAuthenticated();
        $this->assertSame('demo@freelaflow.com', auth()->user()->email);
    }

    public function test_data_isolation_between_users(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Client::factory()->count(2)->create(['user_id' => $userA->id]);
        Client::factory()->count(3)->create(['user_id' => $userB->id]);

        $this->actingAs($userA);
        $this->assertSame(2, Client::count());

        $this->actingAs($userB);
        $this->assertSame(3, Client::count());
    }

    public function test_public_page_rejects_bad_token(): void
    {
        $user = $this->authUser();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $this->get("/p/invoice/{$invoice->id}/invalid-token")->assertStatus(404);
    }
}
