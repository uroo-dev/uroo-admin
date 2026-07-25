<div {{ $attributes->merge(['class' => 'bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover']) }}>
    {{ $slot }}
</div>