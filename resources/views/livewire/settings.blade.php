<div class="space-y-5 p-4 lg:p-6" x-data="{ tab: 'profile' }">

    <x-page-header title="Configurações" subtitle="Gerencie sua conta e preferências" />

    <div class="flex flex-wrap gap-1 border-b border-ink-500/60">
        @foreach(['profile' => 'Perfil', 'company' => 'Empresa', 'financial' => 'Financeiro', 'notifications' => 'Notificações', 'security' => 'Segurança'] as $key => $label)
            <button @click="tab = '{{ $key }}'" @class(['border-b-2 px-4 py-2.5 text-sm font-medium transition', 'border-brand text-brand' => false]) :class="tab === '{{ $key }}' ? 'border-brand text-brand' : 'border-transparent text-gray-400 hover:text-gray-200'">{{ $label }}</button>
        @endforeach
    </div>

    <!-- Profile -->
    <div x-show="tab === 'profile'" class="card p-6">
        <h3 class="mb-4 text-sm font-semibold text-white">Perfil</h3>
        <form wire:submit="saveProfile" class="grid gap-4 sm:grid-cols-2">
            <x-input wire:model="name" label="Nome" required />
            <x-input wire:model="email" label="E-mail" type="email" required />
            <x-input wire:model="phone" label="Telefone" class="sm:col-span-2" />
            <div class="flex justify-end sm:col-span-2">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">Salvar perfil</button>
            </div>
        </form>
    </div>

    <!-- Company -->
    <div x-show="tab === 'company'" class="card p-6" x-cloak>
        <h3 class="mb-4 text-sm font-semibold text-white">Empresa</h3>
        <form wire:submit="saveCompany" class="grid gap-4 sm:grid-cols-2">
            <x-input wire:model="company_name" label="Nome da empresa" />
            <x-input wire:model="document" label="Documento (CNPJ)" />
            <x-input wire:model="address" label="Endereço" />
            <x-input wire:model="postal_code" label="CEP" />
            <x-input wire:model="city" label="Cidade" />
            <x-input wire:model="state" label="Estado" />
            <x-input wire:model="country" label="País" class="sm:col-span-2" />
            <div class="flex justify-end sm:col-span-2">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">Salvar empresa</button>
            </div>
        </form>
    </div>

    <!-- Financial -->
    <div x-show="tab === 'financial'" class="card p-6" x-cloak>
        <h3 class="mb-4 text-sm font-semibold text-white">Financeiro</h3>
        <form wire:submit="saveFinancial" class="grid gap-4 sm:grid-cols-2">
            <x-select wire:model="currency" label="Moeda" :options="collect($currencies)->mapWithKeys(fn($c)=>[$c=>$c])->toArray()" required />
            <div></div>
            <x-textarea wire:model="bank_details" label="Dados bancários" class="sm:col-span-2" />
            <x-textarea wire:model="payment_methods" label="Métodos de pagamento aceitos" class="sm:col-span-2" />
            <div class="flex justify-end sm:col-span-2">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">Salvar financeiro</button>
            </div>
        </form>
    </div>

    <!-- Notifications -->
    <div x-show="tab === 'notifications'" class="card p-6" x-cloak>
        <h3 class="mb-4 text-sm font-semibold text-white">Notificações</h3>
        <div class="space-y-3">
            <label class="flex items-center justify-between rounded-lg border border-ink-500/60 px-4 py-3">
                <span class="text-sm text-gray-300">Notificações por e-mail</span>
                <input type="checkbox" wire:model="notify_email" class="rounded border-ink-500 bg-ink-900 text-brand focus:ring-brand">
            </label>
            <label class="flex items-center justify-between rounded-lg border border-ink-500/60 px-4 py-3">
                <span class="text-sm text-gray-300">Notificações no navegador</span>
                <input type="checkbox" wire:model="notify_browser" class="rounded border-ink-500 bg-ink-900 text-brand focus:ring-brand">
            </label>
        </div>
        <div class="mt-4 flex justify-end">
            <button wire:click="saveNotifications" class="btn-primary" wire:loading.attr="disabled">Salvar preferências</button>
        </div>
    </div>

    <!-- Security -->
    <div x-show="tab === 'security'" class="card p-6" x-cloak>
        <h3 class="mb-4 text-sm font-semibold text-white">Segurança</h3>
        <form wire:submit="changePassword" class="grid gap-4 sm:grid-cols-2">
            <x-input wire:model="current_password" label="Senha atual" type="password" required />
            <div></div>
            <x-input wire:model="new_password" label="Nova senha" type="password" required />
            <x-input wire:model="new_password_confirmation" label="Confirmar nova senha" type="password" required />
            <div class="flex justify-end sm:col-span-2">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">Alterar senha</button>
            </div>
        </form>
        <hr class="my-6 border-ink-500/60">
        <form wire:submit="logoutOtherDevices" class="grid gap-4 sm:grid-cols-2">
            <x-input wire:model="current_password" label="Confirme sua senha" type="password" required />
            <div class="flex items-end">
                <button type="submit" class="btn-danger" wire:loading.attr="disabled">Encerrar outras sessões</button>
            </div>
        </form>
    </div>
</div>
