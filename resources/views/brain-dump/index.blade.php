@extends('layouts.app')

@section('title', 'Brain Dump')
@section('page-title', 'Brain Dump')

@section('content')
    {{-- Quick Add --}}
    <div x-data="{ content: '' }" class="mb-8">
        <x-card>
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-cloud-lightning text-white text-[26px]"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Quick Note</label>
                    <textarea x-model="content" rows="4"
                        placeholder="Apa yang ada di pikiranmu?"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-xs text-txt-secondary" x-text="content.length + ' characters'"></p>
                        <x-button @click="if (content.trim()) { content = '' }" :disabled="!content.trim()">
                            <i class="bx bx-pin"></i> Add Note
                        </x-button>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Search --}}
    <div class="flex items-center justify-between mb-6">
        <div class="relative">
            <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
            <input type="text" placeholder="Search notes..."
                class="w-80 pl-11 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
        </div>
        <p class="text-sm font-semibold text-txt-secondary">12 notes</p>
    </div>

    {{-- Pinned Section --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <i class="bx bx-pin text-[#EF4444] text-xl"></i>
            <h3 class="text-lg font-extrabold">Pinned</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <div
                class="bg-[#FFD93D] border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover"
                x-data="{ open: false }">
                <div class="flex items-start justify-between mb-3">
                    <button @click="open = !open" class="text-lg text-[#EF4444]">
                        <i class="bx bxs-pin"></i>
                    </button>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-archive text-sm text-txt-primary"></i>
                        </button>
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-trash text-sm text-danger"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm font-medium leading-relaxed">
                    Integrate GitHub webhook for auto-deploy on main branch push
                </p>
                <p class="text-[11px] font-semibold text-txt-secondary mt-3">2 hours ago</p>
            </div>
            <div
                class="bg-[#67E8F9] border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover"
                x-data="{ open: false }">
                <div class="flex items-start justify-between mb-3">
                    <button @click="open = !open" class="text-lg text-[#EF4444]">
                        <i class="bx bxs-pin"></i>
                    </button>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-archive text-sm text-txt-primary"></i>
                        </button>
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-trash text-sm text-danger"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm font-medium leading-relaxed">
                    Research about Livewire components for real-time dashboard
                </p>
                <p class="text-[11px] font-semibold text-txt-secondary mt-3">Yesterday</p>
            </div>
        </div>
    </div>

    {{-- All Notes --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <i class="bx bx-notepad text-primary text-xl"></i>
            <h3 class="text-lg font-extrabold">All Notes</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <div
                class="bg-[#FF66C4] border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-3">
                    <button class="text-lg text-txt-secondary hover:text-[#EF4444] transition-colors">
                        <i class="bx bx-pin"></i>
                    </button>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-archive text-sm text-txt-primary"></i>
                        </button>
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-trash text-sm text-danger"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm font-medium leading-relaxed">
                    Create reusable input component with validation state
                </p>
                <p class="text-[11px] font-semibold text-txt-secondary mt-3">3 days ago</p>
            </div>
            <div
                class="bg-[#A855F7] border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-3">
                    <button class="text-lg text-txt-secondary hover:text-[#EF4444] transition-colors">
                        <i class="bx bx-pin"></i>
                    </button>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-archive text-sm text-txt-primary"></i>
                        </button>
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-trash text-sm text-danger"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm font-medium leading-relaxed">
                    Write unit tests for all service classes before next release
                </p>
                <p class="text-[11px] font-semibold text-txt-secondary mt-3">1 week ago</p>
            </div>
            <div
                class="bg-[#22C55E] border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-3">
                    <button class="text-lg text-txt-secondary hover:text-[#EF4444] transition-colors">
                        <i class="bx bx-pin"></i>
                    </button>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-archive text-sm text-txt-primary"></i>
                        </button>
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-trash text-sm text-danger"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm font-medium leading-relaxed">
                    Migrate all enums to PHP 8.1 backed enums
                </p>
                <p class="text-[11px] font-semibold text-txt-secondary mt-3">2 weeks ago</p>
            </div>
            <div
                class="bg-[#FFD93D] border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-3">
                    <button class="text-lg text-txt-secondary hover:text-[#EF4444] transition-colors">
                        <i class="bx bx-pin"></i>
                    </button>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-archive text-sm text-txt-primary"></i>
                        </button>
                        <button class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                            <i class="bx bx-trash text-sm text-danger"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm font-medium leading-relaxed">
                    Setup CI/CD pipeline with GitHub Actions for testing
                </p>
                <p class="text-[11px] font-semibold text-txt-secondary mt-3">2 weeks ago</p>
            </div>
        </div>
    </div>
@endsection
