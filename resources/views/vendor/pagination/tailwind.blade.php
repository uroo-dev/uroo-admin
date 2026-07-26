@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Mobile --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-default leading-5">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-default leading-5">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-txt-secondary leading-5">
                    <span>{!! __('Showing') !!}</span>
                    <span class="font-extrabold text-txt-primary">{{ $paginator->firstItem() }}</span>
                    <span>{!! __('to') !!}</span>
                    <span class="font-extrabold text-txt-primary">{{ $paginator->lastItem() }}</span>
                    <span>{!! __('of') !!}</span>
                    <span class="font-extrabold text-txt-primary">{{ $paginator->total() }}</span>
                    <span>{!! __('results') !!}</span>
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rtl:flex-row-reverse gap-2">
                    {{-- Previous Page Link --}}
                    <span>
                        @if ($paginator->onFirstPage())
                            <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-default leading-5" aria-hidden="true">
                                    <i class="bx bx-chevron-left text-lg"></i>
                                </span>
                            </span>
                        @else
                            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out" aria-label="{{ __('pagination.previous') }}">
                                <i class="bx bx-chevron-left text-lg"></i>
                            </button>
                        @endif
                    </span>

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page">
                                            <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-extrabold text-white bg-primary border-4 border-border-dark rounded-button shadow-hard cursor-default leading-5">{{ $page }}</span>
                                        </span>
                                    @else
                                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                            {{ $page }}
                                        </button>
                                    @endif
                                </span>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    <span>
                        @if ($paginator->hasMorePages())
                            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-primary bg-surface border-4 border-border-dark rounded-button shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out" aria-label="{{ __('pagination.next') }}">
                                <i class="bx bx-chevron-right text-lg"></i>
                            </button>
                        @else
                            <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-txt-secondary bg-surface border-4 border-border-dark rounded-button cursor-default leading-5" aria-hidden="true">
                                    <i class="bx bx-chevron-right text-lg"></i>
                                </span>
                            </span>
                        @endif
                    </span>
                </span>
            </div>
        </div>
    </nav>
@endif
