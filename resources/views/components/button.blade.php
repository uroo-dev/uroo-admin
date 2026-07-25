@props([
    'variant' => 'primary',
    'type' => 'button',
    'size' => 'md',
    'href' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-bold border-4 border-border-dark transition-all duration-200 ease-out cursor-pointer';
    $base .= ' active:translate-y-1 active:shadow-hard-pressed';

    $variants = [
        'primary' => 'bg-primary text-white hover:-translate-y-0.5 shadow-hard',
        'danger' => 'bg-danger text-white hover:-translate-y-0.5 shadow-hard',
        'success' => 'bg-[#22C55E] text-white hover:-translate-y-0.5 shadow-hard',
        'warning' => 'bg-[#F59E0B] text-white hover:-translate-y-0.5 shadow-hard',
        'secondary' => 'bg-surface text-txt-primary hover:-translate-y-0.5 shadow-hard',
        'ghost' => 'bg-transparent text-txt-primary border-transparent hover:bg-gray-100 shadow-none',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-xs rounded-button',
        'md' => 'px-6 py-3 text-sm rounded-button',
        'lg' => 'px-8 py-4 text-base rounded-button',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif