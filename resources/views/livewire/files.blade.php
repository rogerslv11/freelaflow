<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Arquivos" subtitle="Armazene arquivos por cliente e projeto">
        <x-slot name="actions">
            <button class="btn-primary" @click="$store.modal.show('file-form')"><x-icon name="plus" class="w-4 h-4" /> Enviar arquivo</button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-4 lg:grid-cols-4">
        <!-- Folders -->
        <div class="card p-3 lg:col-span-1">
            <p class="px-2 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Pastas</p>
            <button wire:click="setFolder('root')" @class(['flex w-full items-center gap-2 rounded-lg px-2 py-2 text-sm transition', 'bg-brand-soft text-brand' => $folder === 'root', 'text-gray-300 hover:bg-ink-700' => $folder !== 'root'])>{{ $folder === 'root' ? 'Raiz' : 'Raiz' }}</button>
            @foreach($folders as $f)
                @if($f && $f !== 'root')
                    <button wire:click="setFolder('{{ $f }}')" @class(['flex w-full items-center gap-2 rounded-lg px-2 py-2 text-sm transition', 'bg-brand-soft text-brand' => $folder === $f, 'text-gray-300 hover:bg-ink-700' => $folder !== $f])>
                        <x-icon name="files" class="h-4 w-4" /> {{ $f }}
                    </button>
                @endif
            @endforeach
        </div>

        <!-- Files -->
        <div class="card overflow-hidden lg:col-span-3">
            <div class="border-b border-ink-500/60 p-4">
                <div class="relative max-w-md">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"><x-icon name="search" class="w-4 h-4" /></span>
                    <input wire:model.live="search" type="text" placeholder="Buscar arquivos..." class="input pl-9">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-ink-500/60 bg-ink-900/50">
                        <tr><th class="th">Nome</th><th class="th">Tipo</th><th class="th">Tamanho</th><th class="th">Cliente</th><th class="th">Enviado</th><th class="th text-right">Ações</th></tr>
                    </thead>
                    <tbody class="divide-y divide-ink-700/60">
                        @forelse($rows as $file)
                            <tr class="transition hover:bg-ink-700/30">
                                <td class="td">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-ink-700 text-xs font-bold uppercase text-brand">{{ $file->extension }}</span>
                                        <span class="font-medium text-gray-200">{{ $file->original_name }}</span>
                                    </div>
                                </td>
                                <td class="td text-gray-400">{{ $file->mime_type ?? '—' }}</td>
                                <td class="td text-gray-400">{{ $file->human_size }}</td>
                                <td class="td text-gray-400">{{ $file->client?->name ?? '—' }}</td>
                                <td class="td text-gray-400">{{ $file->created_at->format('d/m/Y') }}</td>
                                <td class="td">
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('files.download', $file) }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white" title="Baixar"><x-icon name="arrow-right" class="w-4 h-4 rotate-90" /></a>
                                        <button wire:click="delete({{ $file->id }})" wire:confirm="Excluir arquivo?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-empty-state icon="files" title="Nenhum arquivo" >Envie arquivos para este local.</x-empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-ink-500/60 px-4 py-3">{{ $rows->links() }}</div>
        </div>
    </div>

    <x-modal id="file-form" title="Enviar arquivo" size="lg">
        <form wire:submit="upload" class="space-y-4">
            <div>
                <label class="label">Arquivo</label>
                <input type="file" wire:model="file" class="input file:mr-3 file:rounded-lg file:border-0 file:bg-ink-700 file:px-3 file:py-1.5 file:text-sm file:text-gray-200">
                @error('file') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input wire:model="folderName" label="Pasta" placeholder="Ex: Contratos" />
                <x-select wire:model="client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-select wire:model="project_id" label="Projeto" :options="$projects->pluck('name','id')->toArray()" placeholder="Selecione" class="sm:col-span-2" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled"><span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Enviar</button>
            </div>
        </form>
    </x-modal>
</div>
