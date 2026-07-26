<div>
    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-folder text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Projects</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-code-alt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['development'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Development</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-check-double text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['completed'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Completed</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search projects..."
                        class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <select wire:model.live="category"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Categories</option>
                    <option value="web">Web</option>
                    <option value="mobile">Mobile</option>
                    <option value="api">API</option>
                    <option value="desktop">Desktop</option>
                    <option value="design">Design</option>
                    <option value="other">Other</option>
                </select>
                <select wire:model.live="statusFilter"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Status</option>
                    <option value="development">Development</option>
                    <option value="testing">Testing</option>
                    <option value="revision">Revision</option>
                    <option value="completed">Completed</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <x-button @click="$dispatch('open-modal', { id: 'project-form' })" variant="primary" size="md" class="w-full sm:w-auto">
                <i class="bx bx-plus"></i>
                New Project
            </x-button>
        </div>
    </div>

    {{-- Project Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($projects ?? [] as $project)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-extrabold">{{ $project->name }}</h3>
                        <p class="text-sm text-txt-secondary mt-0.5">{{ $project->client?->name ?? 'No Client' }}</p>
                    </div>
                    <x-badge variant="{{ $project->status }}">{{ ucfirst($project->status) }}</x-badge>
                </div>

                <p class="text-sm text-txt-secondary mb-4 line-clamp-2">{{ $project->description ?? 'No description' }}</p>

                {{-- Tech Stack Tags --}}
                @if ($project->tech_stack ?? false)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach (is_array($project->tech_stack) ? $project->tech_stack : (json_decode($project->tech_stack, true) ?? []) as $tech)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10">
                                {{ $tech }}
                            </span>
                        @endforeach
                    </div>
                @endif

                {{-- Progress --}}
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium">Progress</span>
                        <span class="font-extrabold">{{ $project->progress ?? 0 }}%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all duration-500"
                            style="width: {{ $project->progress ?? 0 }}%"></div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t-4 border-border-dark">
                    <button type="button" wire:click="edit({{ $project->id }})"
                        title="Edit Project"
                        class="p-2 rounded-lg text-txt-secondary hover:bg-primary/10 hover:text-primary hover:scale-110 active:scale-95 transition-all duration-200 ease-out">
                        <i class="bx bx-edit text-lg"></i>
                    </button>
                    <button type="button" wire:click="confirmDelete({{ $project->id }})"
                        title="Delete Project"
                        class="p-2 rounded-lg text-txt-secondary hover:bg-danger/10 hover:text-danger hover:scale-110 active:scale-95 transition-all duration-200 ease-out">
                        <i class="bx bx-trash text-lg"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface border-4 border-border-dark rounded-card shadow-hard p-12">
                <div class="text-center">
                    <i class="bx bx-folder-open text-5xl text-txt-secondary"></i>
                    <p class="text-txt-secondary font-medium mt-3">No projects found</p>
                    <x-button variant="primary" size="sm" class="mt-4" @click="$dispatch('open-modal', { id: 'project-form' })">
                        <i class="bx bx-plus"></i>
                        Create First Project
                    </x-button>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $projects->links() }}
    </div>

    {{-- Modal Form --}}
    <x-modal id="project-form" title="Project Form" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-5">
            @if ($errors->any())
                <div class="p-3 rounded-input border-4 border-danger bg-danger/5 text-sm font-medium text-danger">
                    <i class="bx bx-error-circle align-middle mr-1"></i>
                    <span>Please fix the errors below.</span>
                </div>
            @endif

            <div>
                <x-input label="Project Name" name="name" placeholder="Project name" wire:model="name" />
                @error('name') <p class="text-xs font-medium text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-txt-primary">Description</label>
                <textarea wire:model="description" rows="3" placeholder="Project description..."
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Client</label>
                    <select wire:model="client_id"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="">No Client</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Category</label>
                    <select wire:model="categoryField"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="">Select Category</option>
                        <option value="web">Web</option>
                        <option value="mobile">Mobile</option>
                        <option value="api">API</option>
                        <option value="desktop">Desktop</option>
                        <option value="design">Design</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Status</label>
                    <select wire:model="statusField"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="development">Development</option>
                        <option value="testing">Testing</option>
                        <option value="revision">Revision</option>
                        <option value="completed">Completed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div>
                    <x-input label="Progress (%)" name="progress" type="number" min="0" max="100" wire:model="progress" />
                    @error('progress') <p class="text-xs font-medium text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Tech Stack --}}
            <div x-data="{
                newTech: '',
                addTech() {
                    if (this.newTech.trim()) {
                        $wire.addTech(this.newTech.trim());
                        this.newTech = '';
                    }
                },
                removeTech(index) {
                    $wire.removeTech(index);
                }
            }">
                <label class="block text-sm font-semibold text-txt-primary mb-2">Tech Stack</label>
                <div class="flex items-center gap-2 mb-3">
                    <input type="text" x-model="newTech" @keydown.enter.prevent="addTech" placeholder="Add technology..."
                        class="flex-1 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    <x-button variant="secondary" size="sm" type="button" @click="addTech">
                        <i class="bx bx-plus"></i>
                    </x-button>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($techStack as $index => $tech)
                        <span wire:key="tech-{{ $index }}" class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10">
                            <span>{{ $tech }}</span>
                            <button type="button" @click="removeTech({{ $index }})" class="text-danger hover:text-red-700">
                                <i class="bx bx-x"></i>
                            </button>
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" @click="$dispatch('close-modal', { id: 'project-form' })">
                    Cancel
                </x-button>
                <x-button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save"><i class="bx bx-check"></i> Save Project</span>
                    <span wire:loading wire:target="save"><i class="bx bx-loader-alt animate-spin"></i> Saving...</span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>