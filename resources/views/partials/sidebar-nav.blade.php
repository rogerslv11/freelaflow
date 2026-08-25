@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
        ['label' => 'Clientes', 'route' => 'clients.index', 'icon' => 'clients'],
        ['label' => 'Projetos', 'route' => 'projects.index', 'icon' => 'projects'],
        ['label' => 'Tarefas', 'route' => 'tasks.index', 'icon' => 'tasks'],
        ['label' => 'Propostas', 'route' => 'proposals.index', 'icon' => 'proposals'],
        ['label' => 'Contratos', 'route' => 'contracts.index', 'icon' => 'contracts'],
        ['label' => 'Faturas', 'route' => 'invoices.index', 'icon' => 'invoices'],
        ['label' => 'Financeiro', 'route' => 'finance.index', 'icon' => 'finance'],
        ['label' => 'Agenda', 'route' => 'calendar.index', 'icon' => 'calendar'],
        ['label' => 'Arquivos', 'route' => 'files.index', 'icon' => 'files'],
        ['label' => 'Relatórios', 'route' => 'reports.index', 'icon' => 'reports'],
        ['label' => 'Configurações', 'route' => 'settings.index', 'icon' => 'settings'],
    ];
@endphp

@foreach($items as $item)
    <a
        href="{{ route($item['route']) }}"
        @class([
            'nav-link',
            'nav-link-active' => request()->routeIs(explode('.', $item['route'])[0] . '.*') || request()->routeIs($item['route']),
        ])
    >
        <x-icon :name="$item['icon']" class="w-5 h-5 shrink-0" />
        <span>{{ $item['label'] }}</span>
    </a>
@endforeach
