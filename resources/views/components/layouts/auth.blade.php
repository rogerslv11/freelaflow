<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-200 antialiased bg-[#0A0A0A]">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            <!-- Brand panel -->
            <div class="relative hidden overflow-hidden bg-[#111111] lg:flex lg:flex-col lg:justify-between lg:p-12 border-r border-[#272727]">
                <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-[#FF6B00]/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 -right-16 h-72 w-72 rounded-full bg-[#FF6B00]/10 blur-3xl"></div>

                <div class="relative flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#FF6B00] text-xl font-bold text-white">F</div>
                    <span class="text-lg font-semibold tracking-tight text-white">Freela<span class="text-[#FF6B00]">Flow</span></span>
                </div>

                <div class="relative">
                    <h1 class="max-w-sm text-3xl font-bold leading-tight text-white">
                        Gerencie seu negócio freelancer em um só lugar.
                    </h1>
                    <p class="mt-4 max-w-sm text-sm text-gray-400">
                        Clientes, projetos, propostas, finanças e controle de tempo — com a cara do seu trabalho.
                    </p>

                    <ul class="mt-8 space-y-3 text-sm text-gray-300">
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#FF6B00]/15 text-[#FF6B00]">✓</span>
                            Propostas e contratos prontos para enviar
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#FF6B00]/15 text-[#FF6B00]">✓</span>
                            Faturamento, pagamentos e relatórios
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#FF6B00]/15 text-[#FF6B00]">✓</span>
                            Tarefas e agenda em um calendário
                        </li>
                    </ul>
                </div>

                <p class="relative text-xs text-gray-600">© {{ date('Y') }} FreelaFlow</p>
            </div>

            <!-- Form panel -->
            <div class="flex flex-col items-center justify-center px-6 py-12">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
