@props(['stats'])

@php
    $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    $allDays = [];
    $idx = 0;
    foreach ($stats['weeks'] as $week) {
        foreach ($week as $day) {
            $allDays[] = $day + ['flatIdx' => $idx];
            $idx++;
        }
    }

    $monthSpans = [];
    $lastMonthKey = null;
    $startIdx = 0;
    foreach ($allDays as $i => $day) {
        if ($day['date'] === '') continue;
        $key = \Carbon\Carbon::parse($day['date'])->format('Y-n');
        if ($key !== $lastMonthKey && $lastMonthKey !== null) {
            $monthSpans[] = [
                'label' => \Carbon\Carbon::parse($allDays[$startIdx]['date'])->format('M'),
                'left' => $startIdx * 19,
                'width' => ($i - $startIdx) * 19 - 3,
            ];
            $startIdx = $i;
        }
        $lastMonthKey = $key;
    }
    if ($lastMonthKey !== null) {
        $monthSpans[] = [
            'label' => \Carbon\Carbon::parse($allDays[$startIdx]['date'])->format('M'),
            'left' => $startIdx * 19,
            'width' => (count($allDays) - $startIdx) * 19 - 3,
        ];
    }

    $cellSize = 16;
    $gap = 3;
    $colWidth = $cellSize + $gap;
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
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <div class="inline-flex flex-col gap-1 min-w-[750px]">
            {{-- Month labels --}}
            <div class="ml-10 mb-1" style="position: relative; height: 16px;">
                @foreach ($monthSpans as $m)
                    <span class="text-xs font-bold text-txt-secondary truncate"
                          style="position: absolute; left: {{ $m['left'] }}px; width: {{ $m['width'] }}px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $m['label'] }}
                    </span>
                @endforeach
            </div>

            {{-- Grid --}}
            <div class="flex gap-[3px]">
                {{-- Day labels --}}
                <div class="flex flex-col gap-[3px] mr-2">
                    @foreach ([1, 3, 5] as $dayIndex)
                        <div class="h-[16px] flex items-center">
                            <span class="text-[10px] font-bold text-txt-secondary leading-none">{{ $dayLabels[$dayIndex] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Contribution cells --}}
                <div class="flex gap-[3px]">
                    @foreach ($stats['weeks'] as $week)
                        <div class="flex flex-col gap-[3px]">
                            @foreach ($week as $day)
                                @php
                                    $level = match(true) {
                                        $day['count'] === 0 => 0,
                                        $day['count'] <= 3 => 1,
                                        $day['count'] <= 7 => 2,
                                        $day['count'] <= 15 => 3,
                                        default => 4,
                                    };
                                    $colors = ['#F3F4F6', '#BBF7D0', '#4ADE80', '#22C55E', '#166534'];
                                @endphp
                                <div class="rounded-sm border border-border-dark/20"
                                     style="width: {{ $cellSize }}px; height: {{ $cellSize }}px; background-color: {{ $colors[$level] }}"
                                     title="{{ $day['date'] ?: 'N/A' }}: {{ $day['count'] }} contributions">
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex items-center justify-end gap-2 mt-3">
                <span class="text-[10px] font-bold text-txt-secondary">Less</span>
                @foreach (['#F3F4F6', '#BBF7D0', '#4ADE80', '#22C55E', '#166534'] as $color)
                    <div class="w-[14px] h-[14px] rounded-sm border border-border-dark/20" style="background-color: {{ $color }}"></div>
                @endforeach
                <span class="text-[10px] font-bold text-txt-secondary">More</span>
            </div>
        </div>
    </div>
</div>
