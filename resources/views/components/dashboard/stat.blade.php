@props([
    'title',
    'value',
    'color' => 'secondary'
])

@php
    $colors = [
        'secondary' => 'text-secondary-800',
        'green' => 'text-green-600',
        'blue' => 'text-blue-600',
        'red' => 'text-red-600',
    ];
@endphp

<div class="bg-white rounded-xl border border-secondary-200 p-6">
    <div class="text-sm text-secondary-500">
        {{ $title }}
    </div>
    <div class="text-3xl font-bold {{ $colors[$color] ?? $colors['secondary'] }}">
        {{ $value }}
    </div>
</div>
