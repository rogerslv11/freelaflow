<?php

use App\Livewire\Forms\LoginForm;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Log in as the demo user (seeds one if missing).
     */
    public function demoLogin(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@freelaflow.com'],
            ['name' => 'Demo Freelancer', 'password' => bcrypt('password')],
        );

        if ($user->profile) {
            $user->profile->update(['onboarded' => true]);
        } else {
            Profile::factory()->create([
                'user_id' => $user->id,
                'currency' => 'BRL',
                'onboarded' => true,
            ]);
        }

        Auth::login($user);
        Session::regenerate();

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="rounded-2xl border border-[#272727] bg-[#111111] p-8 shadow-2xl">
    <!-- Mobile brand -->
    <div class="mb-8 flex items-center gap-3 lg:hidden">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#FF6B00] text-lg font-bold text-white">F</div>
        <span class="text-base font-semibold tracking-tight text-white">Freela<span class="text-[#FF6B00]">Flow</span></span>
    </div>

    <h2 class="mb-1 text-xl font-semibold text-white">Bem-vindo de volta</h2>
    <p class="mb-6 text-sm text-gray-400">Entre para gerenciar seu negócio freelancer.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <div class="space-y-4">
            <div>
                <x-input label="Email" name="email" wire:model="form.email" type="email" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <span class="label">Senha</span>
                    @if (Route::has('password.request'))
                        <a class="text-xs font-medium text-[#FF6B00] hover:underline" href="{{ route('password.request') }}" wire:navigate>
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>
                <x-input name="password" wire:model="form.password" type="password" required autocomplete="current-password" class="mt-1.5" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <label for="remember" class="flex items-center gap-2 text-sm text-gray-400">
                <input wire:model="form.remember" id="remember" type="checkbox" class="h-4 w-4 rounded border-[#272727] bg-[#1c1c1c] text-[#FF6B00] focus:ring-[#FF6B00]" name="remember">
                Lembrar de mim
            </label>

            <button type="submit" class="btn-primary w-full justify-center">
                {{ __('Log in') }}
            </button>
        </div>
    </form>

    <div class="my-6 flex items-center gap-3">
        <span class="h-px flex-1 bg-[#272727]"></span>
        <span class="text-xs uppercase tracking-wide text-gray-600">ou</span>
        <span class="h-px flex-1 bg-[#272727]"></span>
    </div>

    <button wire:click="demoLogin" type="button"
            class="flex w-full items-center justify-center gap-2 rounded-lg border border-[#272727] bg-[#181818] px-4 py-2.5 text-sm font-semibold text-gray-200 transition hover:border-[#FF6B00] hover:text-[#FF6B00]">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Entrar como demo
    </button>
    <p class="mt-2 text-center text-xs text-gray-500">
        demo@freelaflow.com · password
    </p>
</div>
