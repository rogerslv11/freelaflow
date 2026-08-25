<div class="w-full max-w-xl">
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-white">Vamos configurar sua conta</h1>
        <p class="mt-1 text-sm text-gray-500">Leva menos de 1 minuto. Etapa {{ $step }} de 5.</p>
    </div>

    <!-- Progress -->
    <div class="mb-8 flex gap-2">
        @for($i = 1; $i <= 5; $i++)
            <div class="h-1.5 flex-1 rounded-full {{ $i <= $step ? 'bg-brand' : 'bg-ink-600' }} transition"></div>
        @endfor
    </div>

    <div class="card p-6 animate-fade-in" wire:key="step-{{ $step }}">
        @switch($step)
            @case(1)
                <h2 class="mb-1 text-lg font-semibold text-white">Qual o nome do seu negócio?</h2>
                <p class="mb-5 text-sm text-gray-500">Usaremos isso em propostas, contratos e faturas.</p>
                <x-input wire:model="companyName" label="Nome profissional / empresa" placeholder="Ex: Ana Studio" required />
            @break

            @case(2)
                <h2 class="mb-5 text-lg font-semibold text-white">Qual seu tipo de atuação?</h2>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach($types as $type)
                        <button type="button" wire:click="$set('freelancerType', '{{ $type }}')"
                            @class([
                                'rounded-lg border px-3 py-3 text-sm font-medium transition',
                                'border-brand bg-brand-soft text-brand' => $freelancerType === $type,
                                'border-ink-500 text-gray-300 hover:border-ink-400' => $freelancerType !== $type,
                            ])>
                            {{ $type }}
                        </button>
                    @endforeach
                </div>
                @error('freelancerType') <p class="mt-2 text-xs text-red-400">{{ $message }}</p> @enderror
            @break

            @case(3)
                <h2 class="mb-5 text-lg font-semibold text-white">Adicione seu primeiro cliente</h2>
                <div class="space-y-4">
                    <x-input wire:model="clientName" label="Nome do cliente" placeholder="Ex: Empresa XYZ" required />
                    <x-input wire:model="clientEmail" label="E-mail" type="email" placeholder="cliente@empresa.com" />
                </div>
            @break

            @case(4)
                <h2 class="mb-5 text-lg font-semibold text-white">Crie seu primeiro projeto</h2>
                <x-input wire:model="projectName" label="Nome do projeto" placeholder="Ex: Site institucional" required />
            @break

            @case(5)
                <h2 class="mb-5 text-lg font-semibold text-white">Qual sua moeda principal?</h2>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($currencies as $c)
                        <button type="button" wire:click="$set('currency', '{{ $c }}')"
                            @class([
                                'rounded-lg border px-4 py-4 text-center text-sm font-semibold transition',
                                'border-brand bg-brand-soft text-brand' => $currency === $c,
                                'border-ink-500 text-gray-300 hover:border-ink-400' => $currency !== $c,
                            ])>
                            {{ $c }}
                        </button>
                    @endforeach
                </div>
            @break
        @endswitch
    </div>

    <div class="mt-6 flex items-center justify-between">
        @if($step > 1)
            <button type="button" wire:click="prev" class="btn-ghost">Voltar</button>
        @else
            <span></span>
        @endif
        <button type="button" wire:click="next" class="btn-primary min-w-32">
            {{ $step === 5 ? 'Concluir' : 'Continuar' }}
        </button>
    </div>
</div>
