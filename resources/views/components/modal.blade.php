@props([
    'id' => 'modal',
    'title' => '',
    'maxWidth' => 'max-w-lg',
])

<div x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open = true"
    x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') open = false"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    style="display: none;">

    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        @click.outside="open = false"
        class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full {{ $maxWidth }} animate-scale-in"
        style="display: none;">

        {{-- Header --}}
        @if ($title)
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
            <h3 class="text-lg font-extrabold">{{ $title }}</h3>
            <button @click="open = false" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                <i class="bx bx-x"></i>
            </button>
        </div>
        @endif

        {{-- Body --}}
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>