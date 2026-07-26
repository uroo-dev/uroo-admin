<div>
    <div class="flex items-center gap-4 mb-3">
        <div class="flex-1 h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
            <div class="h-full bg-[#22C55E] rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
        </div>
        <span class="text-sm font-extrabold">{{ $progress }}%</span>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs font-medium text-txt-secondary">Deploy Readiness:</span>
        @if ($readiness === 'ready')
            <x-badge variant="success">Ready</x-badge>
        @elseif ($readiness === 'almost')
            <x-badge variant="warning">Almost Ready</x-badge>
        @else
            <x-badge variant="danger">Not Ready</x-badge>
        @endif
    </div>
</div>