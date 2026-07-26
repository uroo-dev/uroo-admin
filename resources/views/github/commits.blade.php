<div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h3 class="text-lg font-extrabold">Commit Timeline</h3>
        @if ($repositories->count() > 1)
            <select wire:model.live="repositoryId"
                class="w-full sm:w-auto px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Repositories</option>
                @foreach ($repositories as $repo)
                    <option value="{{ $repo->id }}">{{ $repo->name }}</option>
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
                            <span class="text-xs font-mono font-bold text-txt-secondary bg-gray-100 px-2 py-0.5 rounded">
                                {{ substr($commit->sha, 0, 7) }}
                            </span>
                            <span class="text-xs font-medium text-txt-secondary">
                                {{ $commit->author_name }}
                            </span>
                            @if ($commit->repository)
                                <span class="text-xs font-medium text-primary">{{ $commit->repository->name }}</span>
                            @endif
                        </div>
                        <span class="text-xs font-medium text-txt-secondary sm:ml-auto">
                            {{ $commit->committed_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-sm font-semibold break-words">{{ $commit->message }}</p>
                    @if ($commit->branch)
                        <span class="text-xs font-medium text-txt-secondary mt-1 inline-flex items-center gap-1">
                            <i class="bx bx-git-branch"></i> {{ $commit->branch }}
                        </span>
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