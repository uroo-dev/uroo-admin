@extends('layouts.app')

@section('title', 'GitHub Monitor')
@section('page-title', 'GitHub Monitor')

@section('content')
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
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8"
        x-data="{
            search: '{{ $search }}',
            language: '{{ $language }}',
            sort: '{{ $sortField }}',
            direction: '{{ $sortDirection }}'
        }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h3 class="text-lg font-extrabold">Repositories</h3>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-stretch sm:items-center">
                <form method="GET" action="{{ route('github') }}" id="repo-filter" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-stretch sm:items-center">
                    <select name="language" onchange="this.form.submit()"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                        <option value="">All Languages</option>
                        @foreach ($languages as $lang)
                            <option value="{{ $lang }}" {{ $language === $lang ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                    <div class="relative w-full sm:w-64">
                        <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search repos..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none"
                            x-model.debounce.500ms="search"
                            x-effect="if (search !== '{{ $search }}') { window.location.href = '{{ route('github') }}?search=' + encodeURIComponent(search) + '&language=' + language + '&sort=' + sort + '&direction=' + direction; }">
                    </div>
                    <input type="hidden" name="sort" value="{{ $sortField }}">
                    <input type="hidden" name="direction" value="{{ $sortDirection }}">
                </form>
                <form method="POST" action="{{ route('github.sync') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-2.5 bg-gray-800 text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center justify-center gap-2">
                        <i class="bx bx-sync text-lg"></i>
                        Sync from GitHub
                    </button>
                </form>
            </div>
        </div>

        {{-- Mobile: Card Layout --}}
        <div class="grid grid-cols-1 gap-4 sm:hidden">
            @forelse ($repositories as $repo)
                <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard-hover">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <a href="{{ $repo->url }}" target="_blank" class="font-extrabold text-primary hover:underline flex items-center gap-2 text-sm">
                                <i class="bx {{ $repo->is_private ? 'bx-lock-alt' : 'bx-globe' }} text-txt-secondary flex-shrink-0"></i>
                                <span class="truncate">{{ $repo->name }}</span>
                            </a>
                            @if ($repo->description)
                                <p class="text-xs text-txt-secondary mt-1 line-clamp-2">{{ $repo->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mb-3">
                        @if ($repo->language)
                            <x-badge variant="info">{{ $repo->language }}</x-badge>
                        @endif
                        <span class="flex items-center gap-1 text-xs font-semibold text-txt-secondary">
                            <i class="bx bx-star text-[#F59E0B]"></i> {{ $repo->stars }}
                        </span>
                        <span class="flex items-center gap-1 text-xs font-semibold text-txt-secondary">
                            <i class="bx bx-git-repo-forked"></i> {{ $repo->forks }}
                        </span>
                        <span class="flex items-center gap-1 text-xs font-semibold text-txt-secondary">
                            <i class="bx bx-error-circle"></i> {{ $repo->open_issues }}
                        </span>
                    </div>
                    <div class="text-xs text-txt-secondary font-medium">Updated {{ $repo->last_pushed_at?->diffForHumans() ?? 'N/A' }}</div>
                </div>
            @empty
                <div class="col-span-full bg-surface border-4 border-border-dark rounded-card shadow-hard p-12 text-center">
                    <i class="bx bx-folder-open text-5xl text-txt-secondary"></i>
                    <p class="mt-3 text-txt-secondary font-medium">Belum ada repository</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-primary text-white">
                        @php
                            $sortUrl = fn($field) => route('github', array_merge(request()->query(), ['sort' => $field, 'direction' => $sortField === $field && $sortDirection === 'desc' ? 'asc' : 'desc']));
                        @endphp
                        <th class="text-left px-6 py-4 font-bold text-sm border-r-4 border-border-dark whitespace-nowrap">
                            <a href="{{ $sortUrl('name') }}" class="flex items-center gap-1 text-white hover:text-gray-200">Name <i class="bx bx-sort text-lg"></i></a>
                        </th>
                        <th class="text-left px-6 py-4 font-bold text-sm border-r-4 border-border-dark whitespace-nowrap">Language</th>
                        <th class="text-center px-6 py-4 font-bold text-sm border-r-4 border-border-dark whitespace-nowrap">
                            <a href="{{ $sortUrl('stars') }}" class="flex items-center justify-center gap-1 text-white hover:text-gray-200">Stars <i class="bx bx-sort text-lg"></i></a>
                        </th>
                        <th class="text-center px-6 py-4 font-bold text-sm border-r-4 border-border-dark whitespace-nowrap">
                            <a href="{{ $sortUrl('forks') }}" class="flex items-center justify-center gap-1 text-white hover:text-gray-200">Forks <i class="bx bx-sort text-lg"></i></a>
                        </th>
                        <th class="text-center px-6 py-4 font-bold text-sm border-r-4 border-border-dark whitespace-nowrap">Issues</th>
                        <th class="text-left px-6 py-4 font-bold text-sm whitespace-nowrap">Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($repositories as $repo)
                        <tr class="border-b-2 border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 border-r-2 border-gray-100">
                                <a href="{{ $repo->url }}" target="_blank" class="font-bold text-primary hover:underline flex items-center gap-2 whitespace-nowrap">
                                    <i class="bx {{ $repo->is_private ? 'bx-lock-alt' : 'bx-globe' }} text-txt-secondary"></i>
                                    {{ $repo->name }}
                                </a>
                                @if ($repo->description)
                                    <p class="text-xs text-txt-secondary mt-0.5 truncate max-w-xs">{{ $repo->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 border-r-2 border-gray-100">
                                @if ($repo->language) <x-badge variant="info">{{ $repo->language }}</x-badge> @endif
                            </td>
                            <td class="px-6 py-4 text-center border-r-2 border-gray-100 font-semibold whitespace-nowrap">{{ $repo->stars }}</td>
                            <td class="px-6 py-4 text-center border-r-2 border-gray-100 font-semibold whitespace-nowrap">{{ $repo->forks }}</td>
                            <td class="px-6 py-4 text-center border-r-2 border-gray-100 whitespace-nowrap"><span class="font-semibold">{{ $repo->open_issues }}</span></td>
                            <td class="px-6 py-4 text-sm text-txt-secondary whitespace-nowrap">{{ $repo->last_pushed_at?->diffForHumans() ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center"><i class="bx bx-folder-open text-5xl text-txt-secondary"></i><p class="mt-3 text-txt-secondary font-medium">Belum ada repository</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($repositories->hasPages())
            <div class="mt-4 pt-4 border-t-4 border-border-dark">
                {{ $repositories->links() }}
            </div>
        @endif
    </div>

    {{-- Commit Timeline --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 sm:p-6"
        x-data="{ repoId: '{{ $commitRepoId }}' }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h3 class="text-lg font-extrabold">Commit Timeline</h3>
            @if ($commitRepos->count() > 1)
                <select name="repository_id" onchange="window.location.href='{{ route('github') }}?repository_id=' + this.value"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="">All Repositories</option>
                    @foreach ($commitRepos as $repo)
                        <option value="{{ $repo->id }}" {{ (string) $commitRepoId === (string) $repo->id ? 'selected' : '' }}>{{ $repo->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="space-y-4">
            @forelse ($commits as $commit)
                <div class="flex items-start gap-3 sm:gap-4 p-3 sm:p-4 rounded-button border-2 border-gray-100 hover:border-border-dark transition-colors">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bx-git-commit text-primary text-[16px] sm:text-[20px]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 mb-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-mono font-bold text-txt-secondary bg-gray-100 px-2 py-0.5 rounded">{{ substr($commit->sha, 0, 7) }}</span>
                                <span class="text-xs font-medium text-txt-secondary">{{ $commit->author_name }}</span>
                                @if ($commit->repository)
                                    <span class="text-xs font-medium text-primary">{{ $commit->repository->name }}</span>
                                @endif
                            </div>
                            <span class="text-xs font-medium text-txt-secondary sm:ml-auto">{{ $commit->committed_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm font-semibold break-words">{{ $commit->message }}</p>
                        @if ($commit->branch)
                            <span class="text-xs font-medium text-txt-secondary mt-1 inline-flex items-center gap-1"><i class="bx bx-git-branch"></i> {{ $commit->branch }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <i class="bx bx-git-commit text-5xl text-txt-secondary"></i>
                    <p class="mt-3 text-txt-secondary font-medium">Belum ada commit</p>
                </div>
            @endforelse
        </div>

        @if ($commits->hasPages())
            <div class="mt-4 pt-4 border-t-4 border-border-dark">
                {{ $commits->links() }}
            </div>
        @endif
    </div>
@endsection
