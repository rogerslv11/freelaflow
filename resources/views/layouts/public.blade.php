<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ is_callable($title ?? null) ? $title() : ($title ?? 'FreelaFlow') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ink-900 text-gray-200 antialiased">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="mb-8 flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand font-bold text-white">F</span>
            <span class="text-lg font-semibold text-white">FreelaFlow</span>
        </div>
        {{ $slot }}
        <footer class="mt-10 text-center text-xs text-gray-600">Documento gerado via FreelaFlow</footer>
    </div>
</body>
</html>
