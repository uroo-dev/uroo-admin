@extends('layouts.app')

@section('title', 'App Ideas')
@section('page-title', 'App Ideas')

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-bulb text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">24</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Ideas</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-edit-alt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">8</p>
                    <p class="text-sm font-medium text-txt-secondary">Draft</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-code-alt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">10</p>
                    <p class="text-sm font-medium text-txt-secondary">Development</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-400 rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-archive text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">6</p>
                    <p class="text-sm font-medium text-txt-secondary">Archived</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" placeholder="Search ideas..."
                    class="w-64 pl-11 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <select class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="development">Development</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
            </select>
            <select class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                <option value="">All Platforms</option>
                <option value="web">Web</option>
                <option value="mobile">Mobile</option>
                <option value="desktop">Desktop</option>
                <option value="cli">CLI</option>
            </select>
        </div>
        <x-button @click="$dispatch('open-modal', { id: 'idea-modal' })">
            <i class="bx bx-plus"></i> New Idea
        </x-button>
    </div>

    {{-- Ideas Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-card>
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-lg font-extrabold">TaskFlow</h3>
                    <p class="text-sm text-txt-secondary mt-1">Kanban board with AI-powered task estimation</p>
                </div>
                <div class="flex gap-1">
                    <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <x-badge variant="info">Web</x-badge>
                <x-badge variant="development">Development</x-badge>
            </div>
            <div class="flex flex-wrap gap-1.5 mb-4">
                <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">Laravel</span>
                <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">React</span>
                <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">AI</span>
            </div>
            <div class="flex items-center gap-2 pt-4 border-t-4 border-border-dark">
                <x-button variant="ghost" size="sm">
                    <i class="bx bx-edit-alt"></i>
                </x-button>
                <x-button variant="ghost" size="sm">
                    <i class="bx bx-archive"></i>
                </x-button>
                <x-button variant="ghost" size="sm" class="ml-auto">
                    <i class="bx bx-trash text-danger"></i>
                </x-button>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-lg font-extrabold">CodeSnap</h3>
                    <p class="text-sm text-txt-secondary mt-1">Beautiful code snippet sharing platform</p>
                </div>
                <div class="flex gap-1">
                    <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <x-badge variant="info">Web</x-badge>
                <x-badge variant="warning">Draft</x-badge>
            </div>
            <div class="flex flex-wrap gap-1.5 mb-4">
                <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">Vue</span>
                <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">Tailwind</span>
            </div>
            <div class="flex items-center gap-2 pt-4 border-t-4 border-border-dark">
                <x-button variant="ghost" size="sm">
                    <i class="bx bx-edit-alt"></i>
                </x-button>
                <x-button variant="ghost" size="sm">
                    <i class="bx bx-archive"></i>
                </x-button>
                <x-button variant="ghost" size="sm" class="ml-auto">
                    <i class="bx bx-trash text-danger"></i>
                </x-button>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-lg font-extrabold">GitMetrics</h3>
                    <p class="text-sm text-txt-secondary mt-1">GitHub analytics and developer productivity tracker</p>
                </div>
                <div class="flex gap-1">
                    <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <x-badge variant="info">Web</x-badge>
                <x-badge variant="completed">Completed</x-badge>
            </div>
            <div class="flex flex-wrap gap-1.5 mb-4">
                <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">Laravel</span>
                <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">Chart.js</span>
                <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">GitHub API</span>
            </div>
            <div class="flex items-center gap-2 pt-4 border-t-4 border-border-dark">
                <x-button variant="ghost" size="sm">
                    <i class="bx bx-edit-alt"></i>
                </x-button>
                <x-button variant="ghost" size="sm">
                    <i class="bx bx-archive"></i>
                </x-button>
                <x-button variant="ghost" size="sm" class="ml-auto">
                    <i class="bx bx-trash text-danger"></i>
                </x-button>
            </div>
        </x-card>
    </div>

    {{-- Create/Edit Modal --}}
    <x-modal id="idea-modal" title="New Idea">
        <form class="space-y-5">
            <x-input name="name" label="Name" placeholder="Idea name" />
            <x-input name="tagline" label="Tagline" placeholder="Short description" />
            <div class="space-y-1.5">
                <label for="description" class="block text-sm font-semibold text-txt-primary">Description</label>
                <textarea name="description" id="description" rows="4"
                    placeholder="Describe your idea..."
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-txt-primary">Features</label>
                <div x-data="{ features: [''] }" class="space-y-2">
                    <template x-for="(feature, index) in features" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="features[index]"
                                placeholder="Add a feature..."
                                class="flex-1 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                            <button type="button" @click="features.splice(index, 1)"
                                class="p-3 rounded-button border-4 border-border-dark bg-danger text-white hover:-translate-y-0.5 shadow-hard transition-all duration-200"
                                x-show="features.length > 1">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="features.push('')"
                        class="text-sm font-bold text-primary hover:underline flex items-center gap-1">
                        <i class="bx bx-plus"></i> Add Feature
                    </button>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-txt-primary">Tech Stack</label>
                <div x-data="{ tags: [] }" class="space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="text" x-model="newTag"
                            @keydown.enter.prevent="if (newTag.trim()) { tags.push(newTag.trim()); newTag = '' }"
                            placeholder="Type and press Enter..."
                            class="flex-1 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="(tag, index) in tags" :key="index">
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold bg-gray-100 border-2 border-border-dark rounded-full">
                                <span x-text="tag"></span>
                                <button type="button" @click="tags.splice(index, 1)" class="text-danger hover:text-red-700">
                                    <i class="bx bx-x"></i>
                                </button>
                            </span>
                        </template>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label for="platform" class="block text-sm font-semibold text-txt-primary">Platform</label>
                    <select name="platform" id="platform"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="web">Web</option>
                        <option value="mobile">Mobile</option>
                        <option value="desktop">Desktop</option>
                        <option value="cli">CLI</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="status" class="block text-sm font-semibold text-txt-primary">Status</label>
                    <select name="status" id="status"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="draft">Draft</option>
                        <option value="development">Development</option>
                        <option value="completed">Completed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="priority" class="block text-sm font-semibold text-txt-primary">Priority</label>
                    <select name="priority" id="priority"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <x-button variant="secondary" @click="$dispatch('close-modal', { id: 'idea-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <i class="bx bx-save"></i> Save Idea
                </x-button>
            </div>
        </form>
    </x-modal>
@endsection
