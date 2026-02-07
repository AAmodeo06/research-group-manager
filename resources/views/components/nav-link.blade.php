@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-primary-500 text-sm font-medium text-primary-600'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-secondary-600 hover:text-primary-600';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
