@props(['status'])

<x-badge :color="status_color($status)" dot>
    {{ status_label($status) }}
</x-badge>
