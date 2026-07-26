<div>
    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h3 class="text-lg font-extrabold">Repositories</h3>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-stretch sm:items-center">
            <button wire:click="confirmSync()"
                class="w-full sm:w-auto px-5 py-2.5 bg-gray-800 text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center justify-center gap-2">
                <i class="bx bx-sync text-lg"></i>
                Sync from GitHub
            </button>
            <select wire:model.live="language"
                class="w-full sm:w-auto px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Languages</option>
                @foreach ($languages as $lang)
                    <option value="{{ $lang }}">{{ $lang }}</option>
                @endforeach
            </select>
            <div class="relative w-full sm:w-64">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search repos..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
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

                <div class="text-xs text-txt-secondary font-medium">
                    Updated {{ $repo->last_pushed_at?->diffForHumans() ?? 'N/A' }}
                </div>
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
                    <th class="text-left px-6 py-4 font-bold text-sm border-r-4 border-border-dark cursor-pointer whitespace-nowrap" wire:click="sortBy('name')">
                        Name <i class="bx bx-sort text-lg"></i>
                    </th>
                    <th class="text-left px-6 py-4 font-bold text-sm border-r-4 border-border-dark whitespace-nowrap">Language</th>
                    <th class="text-center px-6 py-4 font-bold text-sm border-r-4 border-border-dark cursor-pointer whitespace-nowrap" wire:click="sortBy('stars')">
                        Stars <i class="bx bx-sort text-lg"></i>
                    </th>
                    <th class="text-center px-6 py-4 font-bold text-sm border-r-4 border-border-dark cursor-pointer whitespace-nowrap" wire:click="sortBy('forks')">
                        Forks <i class="bx bx-sort text-lg"></i>
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
                            @if ($repo->language)
                                <x-badge variant="info">{{ $repo->language }}</x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center border-r-2 border-gray-100 font-semibold whitespace-nowrap">{{ $repo->stars }}</td>
                        <td class="px-6 py-4 text-center border-r-2 border-gray-100 font-semibold whitespace-nowrap">{{ $repo->forks }}</td>
                        <td class="px-6 py-4 text-center border-r-2 border-gray-100 whitespace-nowrap">
                            <span class="font-semibold">{{ $repo->open_issues }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-txt-secondary whitespace-nowrap">{{ $repo->last_pushed_at?->diffForHumans() ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="bx bx-folder-open text-5xl text-txt-secondary"></i>
                            <p class="mt-3 text-txt-secondary font-medium">Belum ada repository</p>
                        </td>
                    </tr>
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