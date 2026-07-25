@extends('layouts.app')

@section('title', 'GitHub Monitor')
@section('page-title', 'GitHub Monitor')

@section('content')
    @php
        $stats = app(\Modules\GitHub\Services\GitHubService::class)->getStats();
    @endphp

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-800 rounded-button flex items-center justify-center shadow-hard">
                    <i class="bx bx-book-open text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['repos'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Repositories</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard">
                    <i class="bx bx-git-commit text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['commitsToday'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Commits Today</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard">
                    <i class="bx bx-git-pull-request text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['openIssues'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Open Issues</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard">
                    <i class="bx bx-git-branch text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['branches'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Branches</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Repository List --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6">
        @livewire('repository-list')
    </div>
@endsection