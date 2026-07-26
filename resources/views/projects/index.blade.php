@extends('layouts.app')

@section('title', 'Projects')
@section('page-title', 'Projects')

@section('content')
    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-folder text-white text-[28px]"></i></div>
                <div><p class="text-3xl font-extrabold">{{ $stats['total'] ?? 0 }}</p><p class="text-sm font-medium text-txt-secondary">Total Projects</p></div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-code-alt text-white text-[28px]"></i></div>
                <div><p class="text-3xl font-extrabold">{{ $stats['development'] ?? 0 }}</p><p class="text-sm font-medium text-txt-secondary">Development</p></div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-check-double text-white text-[28px]"></i></div>
                <div><p class="text-3xl font-extrabold">{{ $stats['completed'] ?? 0 }}</p><p class="text-sm font-medium text-txt-secondary">Completed</p></div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('projects.index') }}" class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search projects..."
                        class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <select name="category" onchange="this.form.submit()"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Categories</option>
                    <option value="web" {{ $category === 'web' ? 'selected' : '' }}>Web</option>
                    <option value="mobile" {{ $category === 'mobile' ? 'selected' : '' }}>Mobile</option>
                    <option value="api" {{ $category === 'api' ? 'selected' : '' }}>API</option>
                    <option value="desktop" {{ $category === 'desktop' ? 'selected' : '' }}>Desktop</option>
                    <option value="design" {{ $category === 'design' ? 'selected' : '' }}>Design</option>
                    <option value="other" {{ $category === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <select name="status" onchange="this.form.submit()"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Status</option>
                    <option value="development" {{ $status === 'development' ? 'selected' : '' }}>Development</option>
                    <option value="testing" {{ $status === 'testing' ? 'selected' : '' }}>Testing</option>
                    <option value="revision" {{ $status === 'revision' ? 'selected' : '' }}>Revision</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <input type="hidden" name="sort" value="{{ $sortField }}">
                <input type="hidden" name="direction" value="{{ $sortDirection }}">
                <noscript><button type="submit" class="px-4 py-3 bg-primary text-white font-bold text-sm rounded-button">Search</button></noscript>
            </form>
            <button onclick="document.getElementById('project-form').classList.remove('hidden')"
                class="px-5 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 w-full sm:w-auto justify-center">
                <i class="bx bx-plus"></i> New Project
            </button>
        </div>
    </div>

    {{-- Project Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($projects as $project)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-extrabold">{{ $project->name }}</h3>
                        <p class="text-sm text-txt-secondary mt-0.5">{{ $project->client?->name ?? 'No Client' }}</p>
                    </div>
                    @php
                        $statusVariants = ['development' => 'development', 'testing' => 'testing', 'revision' => 'revision', 'completed' => 'completed', 'archived' => 'archived'];
                    @endphp
                    <x-badge variant="{{ $statusVariants[$project->status] ?? 'default' }}">{{ ucfirst($project->status) }}</x-badge>
                </div>
                <p class="text-sm text-txt-secondary mb-4 line-clamp-2">{{ $project->description ?? 'No description' }}</p>
                @if ($project->tech_stack)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach (is_array($project->tech_stack) ? $project->tech_stack : (json_decode($project->tech_stack, true) ?? []) as $tech)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10">{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif
                <div class="space-y-2">
                    <div class="flex justify-between text-sm"><span class="font-medium">Progress</span><span class="font-extrabold">{{ $project->progress ?? 0 }}%</span></div>
                    <div class="w-full h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all duration-500" style="width: {{ $project->progress ?? 0 }}%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t-4 border-border-dark">
                    <button onclick="editProject({{ $project->id }}, '{{ $project->name }}', '{{ addslashes($project->description ?? '') }}', {{ $project->client_id ?? 'null' }}, '{{ $project->category ?? '' }}', '{{ $project->status }}', {{ $project->progress ?? 0 }})"
                        class="p-2 rounded-lg text-txt-secondary hover:bg-primary/10 hover:text-primary hover:scale-110 active:scale-95 transition-all duration-200 ease-out" title="Edit Project">
                        <i class="bx bx-edit text-lg"></i>
                    </button>
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Hapus project?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg text-txt-secondary hover:bg-danger/10 hover:text-danger hover:scale-110 active:scale-95 transition-all duration-200 ease-out" title="Delete Project">
                            <i class="bx bx-trash text-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface border-4 border-border-dark rounded-card shadow-hard p-12">
                <div class="text-center"><i class="bx bx-folder-open text-5xl text-txt-secondary"></i><p class="text-txt-secondary font-medium mt-3">No projects found</p></div>
            </div>
        @endforelse
    </div>

    @if ($projects->hasPages())
        <div class="mt-8">{{ $projects->links() }}</div>
    @endif

    {{-- Project Form Modal --}}
    <div id="project-form" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-2xl animate-scale-in max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-6 border-b-4 border-border-dark">
                <h2 class="text-xl font-extrabold" id="project-form-title">New Project</h2>
                <button onclick="document.getElementById('project-form').classList.add('hidden')" class="text-2xl text-txt-secondary hover:text-danger">&times;</button>
            </div>
            <form method="POST" action="{{ route('projects.store') }}" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="_method" id="project-method" value="POST">
                <input type="hidden" name="id" id="project-id" value="">
                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Project Name</label>
                    <input type="text" name="name" id="project-name" required placeholder="Project name"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Description</label>
                    <textarea name="description" id="project-description" rows="3" placeholder="Project description..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-txt-primary mb-1.5">Client</label>
                        <select name="client_id" id="project-client"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                            <option value="">No Client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-txt-primary mb-1.5">Category</label>
                        <select name="category" id="project-category"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                            <option value="">Select Category</option>
                            <option value="web">Web</option><option value="mobile">Mobile</option><option value="api">API</option>
                            <option value="desktop">Desktop</option><option value="design">Design</option><option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-txt-primary mb-1.5">Status</label>
                        <select name="status" id="project-status"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                            <option value="development">Development</option><option value="testing">Testing</option>
                            <option value="revision">Revision</option><option value="completed">Completed</option><option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-txt-primary mb-1.5">Progress (%)</label>
                        <input type="number" name="progress" id="project-progress" min="0" max="100" value="0"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Tech Stack</label>
                    <div id="tech-stack-list" class="space-y-2 mb-2"></div>
                    <button type="button" onclick="addTechStackItem()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out">
                        <i class="bx bx-plus"></i> Add Tech Stack
                    </button>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('project-form').classList.add('hidden')"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Save Project</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editProject(id, name, description, clientId, category, status, progress) {
            document.getElementById('project-form').classList.remove('hidden');
            document.getElementById('project-form-title').textContent = 'Edit Project';
            document.getElementById('project-id').value = id;
            document.getElementById('project-name').value = name;
            document.getElementById('project-description').value = description;
            document.getElementById('project-client').value = clientId || '';
            document.getElementById('project-category').value = category;
            document.getElementById('project-status').value = status;
            document.getElementById('project-progress').value = progress;
            document.getElementById('project-method').value = 'PUT';
            document.querySelector('#project-form form').action = '{{ url('projects') }}/' + id;
        }
        document.getElementById('project-form')?.addEventListener('hidden', function() {
            document.getElementById('project-form-title').textContent = 'New Project';
            document.getElementById('project-method').value = 'POST';
            document.querySelector('#project-form form').action = '{{ route('projects.store') }}';
        });
    </script>
@endsection
