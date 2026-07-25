<div>
    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h3 class="text-lg font-extrabold">Repositories</h3>
        <div class="flex gap-3">
            <select wire:model.live="language"
                class="px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Languages</option>
                @foreach ($languages as $lang)
                    <option value="{{ $lang }}">{{ $lang }}</option>
                @endforeach
            </select>
            <div class="relative">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search repos..."
                    class="w-64 pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-primary text-white">
                    <th class="text-left px-6 py-4 font-bold text-sm border-r-4 border-border-dark cursor-pointer" wire:click="sortBy('name')">
                        Name <i class="bx bx-sort text-lg"></i>
                    </th>
                    <th class="text-left px-6 py-4 font-bold text-sm border-r-4 border-border-dark">Language</th>
                    <th class="text-center px-6 py-4 font-bold text-sm border-r-4 border-border-dark cursor-pointer" wire:click="sortBy('stars')">
                        Stars <i class="bx bx-sort text-lg"></i>
                    </th>
                    <th class="text-center px-6 py-4 font-bold text-sm border-r-4 border-border-dark cursor-pointer" wire:click="sortBy('forks')">
                        Forks <i class="bx bx-sort text-lg"></i>
                    </th>
                    <th class="text-center px-6 py-4 font-bold text-sm border-r-4 border-border-dark">Issues</th>
                    <th class="text-left px-6 py-4 font-bold text-sm">Updated</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($repositories as $repo)
                    <tr class="border-b-2 border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 border-r-2 border-gray-100">
                            <a href="{{ $repo->url }}" target="_blank" class="font-bold text-primary hover:underline flex items-center gap-2">
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
                        <td class="px-6 py-4 text-center border-r-2 border-gray-100 font-semibold">{{ $repo->stars }}</td>
                        <td class="px-6 py-4 text-center border-r-2 border-gray-100 font-semibold">{{ $repo->forks }}</td>
                        <td class="px-6 py-4 text-center border-r-2 border-gray-100">
                            <span class="font-semibold">{{ $repo->open_issues }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-txt-secondary">{{ $repo->last_pushed_at?->diffForHumans() ?? 'N/A' }}</td>
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