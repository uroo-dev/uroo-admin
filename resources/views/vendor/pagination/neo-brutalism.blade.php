@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">

        {{-- Mobile: Prev / Next only --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-not-allowed opacity-50">
                    &larr; Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out">
                    &larr; Prev
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-4 py-2 ml-3 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out">
                    Next &rarr;
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 ml-3 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-not-allowed opacity-50">
                    Next &rarr;
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            {{-- Info text --}}
            <div>
                <p class="text-sm font-medium text-txt-secondary">
                    Showing
                    <span class="font-extrabold text-txt-primary">{{ $paginator->firstItem() }}</span>
                    –
                    <span class="font-extrabold text-txt-primary">{{ $paginator->lastItem() }}</span>
                    of
                    <span class="font-extrabold text-txt-primary">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            {{-- Page buttons --}}
            <div class="flex items-center gap-2">

                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-not-allowed opacity-50" aria-disabled="true">
                        <i class="bx bx-chevron-left text-lg"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out" aria-label="Previous">
                        <i class="bx bx-chevron-left text-lg"></i>
                    </a>
                @endif

                {{-- Page numbers --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-secondary cursor-default">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center w-10 h-10 text-sm font-extrabold text-white bg-primary border-4 border-border-dark rounded-button shadow-hard cursor-default" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out" aria-label="Go to page {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out" aria-label="Next">
                        <i class="bx bx-chevron-right text-lg"></i>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-not-allowed opacity-50" aria-disabled="true">
                        <i class="bx bx-chevron-right text-lg"></i>
                    </span>
                @endif

            </div>
        </div>
    </nav>
@endif
