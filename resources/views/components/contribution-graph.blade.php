@props(['stats'])

@php
    $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $cellSize = 14;
    $gap = 2;
    $step = $cellSize + $gap;

    $ghColors = ['#ebedf0', '#9be9a8', '#40c463', '#30a14e', '#216e39'];

    $weekWidth = 7 * $cellSize + 6 * $gap;
@endphp

<div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard-sm">
                <i class="bx bx-calendar-check text-white text-2xl"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-txt-primary">{{ number_format($stats['total']) }}</p>
                <p class="text-sm font-medium text-txt-secondary">contributions in the last year</p>
                @if (($stats['source'] ?? '') === 'local')
                    <p class="text-[10px] font-medium text-txt-secondary/60">⚠ using local data (GitHub GraphQL unavailable)</p>
                @endif
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <div class="inline-flex flex-col min-w-[750px]">
            {{-- Month labels row --}}
            <div class="flex mb-[2px]">
                {{-- Spacer for day labels --}}
                <div class="flex flex-col mr-[4px]" style="gap: {{ $gap }}px;">
                    @foreach ([1, 3, 5] as $dayIndex)
                        <div style="height: {{ $cellSize }}px;"></div>
                    @endforeach
                </div>

                {{-- Month per week column --}}
                <div class="flex" style="gap: {{ $gap }}px;">
                    @foreach ($stats['weeks'] as $wIdx => $week)
                        <div style="width: {{ $weekWidth }}px; height: 13px; position: relative;">
                            @php
                                $newMonth = null;
                                $firstReal = null;
                                foreach ($week as $i => $day) {
                                    if (empty($day['date'])) continue;
                                    $d = \Carbon\Carbon::parse($day['date']);
                                    if ($firstReal === null) $firstReal = ['date' => $d, 'idx' => $i];
                                    if ($d->day === 1) {
                                        $newMonth = [
                                            'label' => $d->format('M'),
                                            'left' => $i * $step,
                                        ];
                                        break;
                                    }
                                }
                                if ($newMonth === null && $firstReal && $wIdx === 0) {
                                    $newMonth = [
                                        'label' => $firstReal['date']->format('M'),
                                        'left' => $firstReal['idx'] * $step,
                                    ];
                                }
                            @endphp
                            @if ($newMonth)
                                <span class="text-[11px] font-semibold text-txt-secondary"
                                      style="position: absolute; left: {{ $newMonth['left'] }}px; top: 0; white-space: nowrap;">
                                    {{ $newMonth['label'] }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Grid --}}
            <div class="flex">
                {{-- Day labels --}}
                <div class="flex flex-col mr-[4px]" style="gap: {{ $gap }}px;">
                    @foreach ([1, 3, 5] as $dayIndex)
                        <div style="height: {{ $cellSize }}px; display: flex; align-items: center;">
                            <span class="text-[10px] font-medium text-txt-secondary leading-none">{{ $dayLabels[$dayIndex] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Contribution cells --}}
                <div class="flex" style="gap: {{ $gap }}px;">
                    @foreach ($stats['weeks'] as $week)
                        <div class="flex flex-col" style="gap: {{ $gap }}px;">
                            @foreach ($week as $day)
                                @php
                                    $level = match(true) {
                                        $day['count'] === 0 => 0,
                                        $day['count'] <= 3 => 1,
                                        $day['count'] <= 7 => 2,
                                        $day['count'] <= 15 => 3,
                                        default => 4,
                                    };
                                @endphp
                                <div style="width: {{ $cellSize }}px; height: {{ $cellSize }}px; border-radius: 2px; background-color: {{ $ghColors[$level] }}"
                                     title="{{ $day['date'] ?: 'N/A' }}: {{ $day['count'] }} {{ $day['count'] === 1 ? 'contribution' : 'contributions' }}">
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex items-center justify-end gap-[2px] mt-[8px]">
                <span class="text-[10px] font-medium text-txt-secondary mr-[4px]">Less</span>
                @foreach ($ghColors as $color)
                    <div style="width: 12px; height: 12px; border-radius: 2px; background-color: {{ $color }}"></div>
                @endforeach
                <span class="text-[10px] font-medium text-txt-secondary ml-[4px]">More</span>
            </div>
        </div>
    </div>
</div>
