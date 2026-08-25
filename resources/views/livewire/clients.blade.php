<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Clientes" subtitle="Gerencie todos os seus clientes">
        <x-slot name="actions">
            <button class="btn-primary" wire:click="create">
                <x-icon name="plus" class="w-4 h-4" /> Novo cliente
            </button>
        </x-slot>
    </x-page-header>

    <!-- Filters -->
    <div class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                <x-icon name="search" class="w-4 h-4" />
            </span>
            <input wire:model.live="search" type="text" placeholder="Buscar clientes..." class="input pl-9">
        </div>
        <select wire:model.live="statusFilter" class="input sm:w-44">
            <option value="">Todos os status</option>
            @foreach($statuses as $s)
                <option value="{{ $s }}">{{ status_label($s) }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-ink-500/60 bg-ink-900/50">
                    <tr>
                        <th class="th">Nome</th>
                        <th class="th">Empresa</th>
                        <th class="th">E-mail</th>
                        <th class="th">Status</th>
                        <th class="th">Criado em</th>
                        <th class="th text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($rows as $client)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td">
                                <a href="{{ route('clients.show', $client) }}" class="flex items-center gap-3 group">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-ink-700 text-xs font-semibold text-brand">{{ $client->initials }}</span>
                                    <span class="font-medium text-gray-200 group-hover:text-brand">{{ $client->name }}</span>
                                </a>
                            </td>
                            <td class="td">{{ $client->company ?? '—' }}</td>
                            <td class="td">{{ $client->email ?? '—' }}</td>
                            <td class="td"><x-status-badge :status="$client->status" /></td>
                            <td class="td text-gray-500">{{ $client->created_at->format('d/m/Y') }}</td>
                            <td class="td">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('clients.show', $client) }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white" title="Ver">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    <button wire:click="edit({{ $client->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white" title="Editar">
                                        <x-icon name="edit" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $client->id }})" wire:confirm="Excluir este cliente?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400" title="Excluir">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="clients" title="Nenhum cliente encontrado" >Clique em "Novo cliente" para começar.</x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-ink-500/60 px-4 py-3">
            {{ $rows->links() }}
        </div>
    </div>

    <!-- Form modal -->
    <x-modal id="client-form" :title="$editingId ? 'Editar cliente' : 'Novo cliente'" size="lg">
        <form wire:submit="save" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input wire:model="name" label="Nome" required />
                <x-input wire:model="company" label="Empresa" />
                <x-input wire:model="email" label="E-mail" type="email" />
                <x-input wire:model="phone" label="Telefone" />
                <x-input wire:model="whatsapp" label="WhatsApp" />
                <x-input wire:model="document" label="Documento (CPF/CNPJ)" />
                <x-input wire:model="city" label="Cidade" />
                <x-input wire:model="state" label="Estado" />
                <x-input wire:model="address" label="Endereço" />
                <x-input wire:model="country" label="País" />
            </div>
            <x-select wire:model="status" label="Status" :options="collect($statuses)->mapWithKeys(fn($s)=>[$s=>status_label($s)])->toArray()" required />
            <x-textarea wire:model="notes" label="Observações" />
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                    <span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    Salvar
                </button>
            </div>
        </form>
    </x-modal>
</div>
