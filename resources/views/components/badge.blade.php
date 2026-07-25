@props([
    'variant' => 'default',
])

@php
    $variants = [
        'development' => 'bg-secondary text-white',
        'testing' => 'bg-[#F59E0B] text-white',
        'revision' => 'bg-orange-500 text-white',
        'completed' => 'bg-[#22C55E] text-white',
        'archived' => 'bg-gray-400 text-white',
        'production' => 'bg-purple-acc text-white',
        'success' => 'bg-[#22C55E] text-white',
        'warning' => 'bg-[#F59E0B] text-white',
        'danger' => 'bg-danger text-white',
        'info' => 'bg-secondary text-white',
        'default' => 'bg-gray-200 text-txt-primary',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full ' . ($variants[$variant] ?? $variants['default'])]) }}>
    {{ $slot }}
</span>