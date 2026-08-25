<div class="space-y-6 p-4 lg:p-6">

    <x-page-header title="Agenda" subtitle="Seus eventos, prazos e entregas">
        <x-slot name="actions">
            <button wire:click="create" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Novo evento</button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between gap-2 border-b border-[#272727] p-4">
                    <h2 class="text-base font-semibold text-gray-100">
                        {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
                    </h2>
                    <div class="flex items-center gap-1">
                        <button wire:click="prevMonth" class="rounded-md bg-[#1c1c1c] p-2 text-gray-400 hover:bg-[#272727] hover:text-white"><x-icon name="chevron-left" class="h-4 w-4" /></button>
                        <button wire:click="goToCurrentMonth" class="rounded-md bg-[#1c1c1c] px-3 py-2 text-xs font-medium text-gray-300 hover:bg-[#272727] hover:text-white">Hoje</button>
                        <button wire:click="nextMonth" class="rounded-md bg-[#1c1c1c] p-2 text-gray-400 hover:bg-[#272727] hover:text-white"><x-icon name="chevron-right" class="h-4 w-4" /></button>
                    </div>
                </div>

                <div class="grid grid-cols-7 bg-[#0F0F0F] text-center text-[11px] font-medium uppercase tracking-wide text-gray-600">
                    @foreach(['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'] as $d)
                        <div class="border-b border-r border-[#272727] py-2">{{ $d }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7">
                    @foreach($grid as $week)
                        @foreach($week as $date)
                            @if($date)
                                @php
                                    $key = $date->format('Y-m-d');
                                    $isToday = $date->isToday();
                                    $dayEvents = $eventsByDay[$key] ?? collect();
                                @endphp
                                <div class="group relative min-h-[96px] border-b border-r border-[#272727] bg-[#111111] p-1.5 transition-colors hover:bg-[#161616] {{ $isToday ? 'bg-[#1a1206]' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs {{ $isToday ? 'flex h-5 w-5 items-center justify-center rounded-full bg-[#FF6B00] font-semibold text-white' : 'text-gray-500' }}">{{ $date->day }}</span>
                                        <button wire:click="create('{{ $key }}')" class="hidden rounded p-1 text-gray-500 hover:bg-[#FF6B00] hover:text-white group-hover:block" title="Novo evento">
                                            <x-icon name="plus" class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                    <div class="mt-1 space-y-1">
                                        @foreach($dayEvents->take(3) as $event)
                                            @php $meta = event_type_meta($event->type); @endphp
                                            <button wire:click="edit({{ $event->id }})" class="flex w-full items-center gap-1 truncate rounded border px-1 py-0.5 text-left text-[11px] hover:opacity-80 {{ $meta['class'] }}">
                                                @if(!$event->all_day)<span class="font-medium">{{ $event->start_at->format('H:i') }}</span>@endif
                                                <span class="truncate">{{ $event->title }}</span>
                                            </button>
                                        @endforeach
                                        @if($dayEvents->count() > 3)
                                            <div class="px-1 text-[10px] text-gray-500">+{{ $dayEvents->count() - 3 }} mais</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="min-h-[96px] border-b border-r border-[#272727] bg-[#0d0d0d]"></div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-5">
                <h3 class="mb-3 text-sm font-semibold text-gray-200">Próximos eventos</h3>
                @if($upcomingEvents->count() > 0)
                    <div class="space-y-2">
                        @foreach($upcomingEvents as $event)
                            @php $meta = event_type_meta($event->type); @endphp
                            <button wire:click="edit({{ $event->id }})" class="flex w-full items-start gap-3 rounded-lg border border-[#272727] bg-[#0F0F0F] p-3 text-left transition-colors hover:border-[#FF6B00]">
                                <div class="flex w-12 shrink-0 flex-col items-center rounded-md border py-1.5 {{ $meta['class'] }}">
                                    <span class="text-xs font-semibold uppercase">{{ $event->start_at->translatedFormat('M') }}</span>
                                    <span class="text-lg font-bold leading-none">{{ $event->start_at->format('d') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-100">{{ $event->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $event->all_day ? 'Dia inteiro' : $event->start_at->format('H:i') }} ·
                                        {{ $meta['label'] }}
                                        @if($event->client)· {{ $event->client->name }}@endif
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <x-empty-state title="Nada por aqui" description="Não há eventos futuros agendados." />
                @endif
            </div>

            <div class="card p-5">
                <h3 class="mb-3 text-sm font-semibold text-gray-200">Legenda</h3>
                <div class="space-y-2">
                    @foreach(App\Models\CalendarEvent::TYPES as $type)
                        @php $meta = event_type_meta($type); @endphp
                        <div class="flex items-center gap-2 text-sm">
                            <span class="h-3 w-3 rounded-sm {{ $meta['class'] }}"></span>
                            <span class="text-gray-300">{{ $meta['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <x-modal id="event-form" :title="$editingId ? 'Editar evento' : 'Novo evento'" size="lg">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="label">Título</label>
                <x-input wire:model="title" placeholder="Ex.: Reunião de kickoff" />
                @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Tipo</label>
                    <x-select wire:model="type" :options="collect(App\Models\CalendarEvent::TYPES)->mapWithKeys(fn($t)=>[$t=>event_type_meta($t)['label']])->all()" />
                    @error('type') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end">
                    <label class="flex w-full items-center gap-2 rounded-lg border border-[#272727] bg-[#0F0F0F] p-3 text-sm text-gray-300">
                        <input type="checkbox" wire:model.live="all_day" class="h-4 w-4 rounded border-[#272727] bg-[#1c1c1c] text-[#FF6B00] focus:ring-[#FF6B00]">
                        Dia inteiro
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Início</label>
                    <x-input wire:model="starts_at" type="datetime-local" />
                    @error('starts_at') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Fim</label>
                    <x-input wire:model="ends_at" type="datetime-local" />
                    @error('ends_at') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            @if(!$all_day)
                <p class="text-xs text-gray-500">Dica: use "Dia inteiro" para eventos sem horário definido.</p>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Cliente</label>
                    <x-select wire:model="client_id" :options="$clients->pluck('name','id')->toArray()" placeholder="—" />
                </div>
                <div>
                    <label class="label">Projeto</label>
                    <x-select wire:model="project_id" :options="$projects->pluck('name','id')->toArray()" placeholder="—" />
                </div>
            </div>

            <div>
                <label class="label">Descrição</label>
                <x-textarea wire:model="description" rows="3" placeholder="Detalhes do evento..." />
            </div>

            <div class="flex items-center justify-between pt-2">
                <div>
                    @if($editingId)
                        <button type="button" wire:click="delete({{ $editingId }})" wire:confirm="Excluir este evento?" class="text-sm text-red-400 hover:text-red-300">Excluir</button>
                    @endif
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </x-modal>
</div>
