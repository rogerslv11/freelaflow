<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ is_callable($title ?? null) ? $title() : ($title ?? 'Painel') }} · FreelaFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-ink-900 text-gray-200 antialiased"
      x-data
      :class="{ 'overflow-hidden': $store.sidebar.open }">

<div class="flex h-full">

    <!-- Sidebar (desktop) -->
    <aside class="hidden lg:flex w-64 shrink-0 flex-col border-r border-ink-500/60 bg-ink-900">
        <div class="flex h-16 items-center gap-2.5 px-5 border-b border-ink-500/60">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand text-white font-bold text-lg shadow-glow">F</div>
            <span class="text-base font-bold tracking-tight text-white">Freela<span class="text-brand">Flow</span></span>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            @include('partials.sidebar-nav')
        </nav>

        @include('partials.sidebar-user')
    </aside>

    <!-- Mobile sidebar -->
    <div x-show="$store.sidebar.open" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div x-show="$store.sidebar.open" x-transition.opacity class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="$store.sidebar.close()"></div>
        <aside x-show="$store.sidebar.open" x-transition class="absolute left-0 top-0 flex h-full w-64 flex-col border-r border-ink-500/60 bg-ink-900">
            <div class="flex h-16 items-center justify-between px-5 border-b border-ink-500/60">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand text-white font-bold text-lg shadow-glow">F</div>
                    <span class="text-base font-bold text-white">Freela<span class="text-brand">Flow</span></span>
                </div>
                <button @click="$store.sidebar.close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-ink-700 hover:text-white">
                    <x-icon name="x" class="w-5 h-5"/>
                </button>
            </div>
            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                @include('partials.sidebar-nav')
            </nav>
            @include('partials.sidebar-user')
        </aside>
    </div>

    <!-- Main -->
    <div class="flex min-w-0 flex-1 flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-ink-500/60 bg-ink-900/80 px-4 backdrop-blur lg:px-6">
            <button @click="$store.sidebar.toggle()" class="rounded-lg p-2 text-gray-400 hover:bg-ink-700 hover:text-white lg:hidden">
                <x-icon name="menu" class="w-5 h-5"/>
            </button>

            <div class="hidden md:block min-w-0">
                @isset($breadcrumb)
                    <nav class="flex items-center gap-1.5 text-sm text-gray-500 truncate">
                        @foreach($breadcrumb as $crumb)
                            @if($loop->last)
                                <span class="font-medium text-gray-200">{{ $crumb }}</span>
                            @else
                                <span>{{ $crumb }}</span>
                                <span class="text-ink-400">/</span>
                            @endif
                        @endforeach
                    </nav>
                @else
                    <h1 class="text-base font-semibold text-white truncate">{{ $header ?? '' }}</h1>
                @endisset
            </div>

            <div class="flex flex-1 items-center justify-end gap-2">
                <livewire:global-search />
                <livewire:notifications-menu />
                <livewire:header-user-menu />
            </div>
        </header>

        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

<x-toaster />
@livewireScripts
</body>
</html>
