<?php

if (! function_exists('status_label')) {
    function status_label(string $status): string
    {
        return match ($status) {
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'lead' => 'Lead',
            'planning' => 'Planejamento',
            'in_progress' => 'Em andamento',
            'review' => 'Em revisão',
            'paused' => 'Pausado',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado',
            'todo' => 'A fazer',
            'done' => 'Concluída',
            'draft' => 'Rascunho',
            'sent' => 'Enviada',
            'viewed' => 'Visualizada',
            'accepted' => 'Aceita',
            'rejected' => 'Recusada',
            'expired' => 'Expirada',
            'pending' => 'Pendente',
            'paid' => 'Paga',
            'overdue' => 'Vencida',
            'ended' => 'Encerrado',
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            'urgent' => 'Urgente',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
}

if (! function_exists('status_color')) {
    function status_color(string $status): string
    {
        return match ($status) {
            'active', 'completed', 'accepted', 'paid', 'done' => 'green',
            'inactive', 'cancelled', 'rejected', 'overdue' => 'red',
            'lead', 'pending', 'review', 'viewed', 'paused', 'ended' => 'amber',
            'planning', 'in_progress', 'draft', 'sent', 'expired' => 'blue',
            'urgent', 'high' => 'red',
            'medium' => 'amber',
            'low' => 'gray',
            default => 'gray',
        };
    }
}

if (! function_exists('money')) {
    function money($amount, ?string $currency = null): string
    {
        $currency = $currency ?? (auth()->check() ? (auth()->user()->profile?->currency ?? 'BRL') : 'BRL');
        $symbols = ['BRL' => 'R$', 'USD' => '$', 'EUR' => '€'];
        $symbol = $symbols[$currency] ?? 'R$';
        $value = is_numeric($amount) ? (float) $amount : 0;

        return $symbol.' '.number_format($value, 2, ',', '.');
    }
}

if (! function_exists('priority_color')) {
    function priority_color(string $priority): string
    {
        return match ($priority) {
            'urgent' => 'red',
            'high' => 'amber',
            'medium' => 'blue',
            default => 'gray',
        };
    }
}

if (! function_exists('priority_dot_class')) {
    function priority_dot_class(string $priority): string
    {
        return match ($priority) {
            'urgent' => 'bg-red-500',
            'high' => 'bg-amber-500',
            'medium' => 'bg-blue-500',
            default => 'bg-gray-500',
        };
    }
}

if (! function_exists('priority_badge_class')) {
    function priority_badge_class(string $priority): string
    {
        return match ($priority) {
            'urgent' => 'bg-red-500/15 text-red-400',
            'high' => 'bg-amber-500/15 text-amber-400',
            'medium' => 'bg-blue-500/15 text-blue-400',
            default => 'bg-gray-500/15 text-gray-400',
        };
    }
}

if (! function_exists('duration_human')) {
    function duration_human(int $seconds): string
    {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        if ($h >= 24) {
            $d = floor($h / 24);

            return $d.'d '.($h % 24).'h';
        }

        return $h.'h '.str_pad($m, 2, '0', STR_PAD_LEFT).'m';
    }
}

if (! function_exists('event_type_meta')) {
    function event_type_meta(string $type): array
    {
        return match ($type) {
            'meeting' => ['label' => 'Reunião', 'class' => 'bg-blue-500/15 text-blue-400 border-blue-500/30'],
            'deadline' => ['label' => 'Prazo', 'class' => 'bg-red-500/15 text-red-400 border-red-500/30'],
            'delivery' => ['label' => 'Entrega', 'class' => 'bg-brand-soft text-brand border-brand/30'],
            'task' => ['label' => 'Tarefa', 'class' => 'bg-purple-500/15 text-purple-400 border-purple-500/30'],
            default => ['label' => 'Evento', 'class' => 'bg-ink-700 text-gray-300 border-ink-500'],
        };
    }
}
