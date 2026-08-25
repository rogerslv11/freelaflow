<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Boas-vindas · FreelaFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-ink-900 text-gray-200 antialiased">
    <div class="flex min-h-full flex-col">
        <div class="flex h-16 items-center gap-2.5 px-6">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand text-white font-bold text-lg shadow-glow">F</div>
            <span class="text-base font-bold tracking-tight text-white">Freela<span class="text-brand">Flow</span></span>
        </div>
        <div class="flex flex-1 items-center justify-center px-4 py-8">
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>
</html>
