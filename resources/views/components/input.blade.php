@props([
    'label' => null,
    'error' => null,
    'type' => 'text',
    'name' => null,
    'placeholder' => '',
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-txt-primary">{{ $label }}</label>
    @endif
    <input type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors']) }}>
    @if ($error)
        <p class="text-xs font-medium text-danger">{{ $error }}</p>
    @endif
</div>