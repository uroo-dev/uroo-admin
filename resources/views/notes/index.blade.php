@extends('layouts.app')

@section('title', 'Developer Notes')
@section('page-title', 'Developer Notes')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex gap-3 flex-wrap">
            <div class="relative">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search notes..."
                    class="w-64 pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
            <select wire:model.live="category"
                class="px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <button onclick="Livewire.dispatch('open-modal', { id: 'note-form' })"
            class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
            <i class="bx bx-plus text-lg"></i>
            New Note
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($notes as $note)
            <div onclick="Livewire.dispatch('open-modal', { id: 'note-edit-{{ $note->id }}' })"
                class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover cursor-pointer group">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2 min-w-0">
                        @if ($note->is_pinned)
                            <i class="bx bxs-pin text-primary text-lg flex-shrink-0"></i>
                        @endif
                        <h3 class="font-bold text-sm truncate">{{ $note->title }}</h3>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button wire:click.stop="togglePin({{ $note->id }})"
                            class="text-lg {{ $note->is_pinned ? 'text-primary' : 'text-txt-secondary' }} hover:text-primary transition-colors">
                            <i class="bx {{ $note->is_pinned ? 'bxs-pin' : 'bx-pin' }}"></i>
                        </button>
                        <button wire:click.stop="toggleFavorite({{ $note->id }})"
                            class="text-lg {{ $note->is_favorite ? 'text-[#F59E0B]' : 'text-txt-secondary' }} hover:text-[#F59E0B] transition-colors">
                            <i class="bx {{ $note->is_favorite ? 'bxs-star' : 'bx-star' }}"></i>
                        </button>
                    </div>
                </div>

                <p class="text-sm text-txt-secondary line-clamp-3 mb-4">
                    {{ Str::limit(strip_tags($note->content), 120) }}
                </p>

                <div class="flex items-center justify-between pt-3 border-t-4 border-border-dark">
                    <x-badge variant="{{ $note->category === 'Important' ? 'danger' : ($note->category === 'Idea' ? 'warning' : ($note->category === 'Tutorial' ? 'info' : 'default')) }}">
                        {{ $note->category ?? 'General' }}
                    </x-badge>
                    <span class="text-xs text-txt-secondary">{{ $note->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-note text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No notes yet</h3>
                <p class="text-txt-secondary mt-2">Create your first note</p>
                <button onclick="Livewire.dispatch('open-modal', { id: 'note-form' })"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + New Note
                </button>
            </div>
        @endforelse
    </div>

    @if ($notes->hasPages())
        <div class="mt-6">
            {{ $notes->links() }}
        </div>
    @endif

    <x-modal id="note-form" title="Create Note">
        <form wire:submit="save" class="space-y-4">
            <x-input label="Title" name="title" placeholder="Note title..." wire:model="title" />
            <div>
                <label for="content" class="block text-sm font-semibold text-txt-primary mb-1.5">Content</label>
                <textarea wire:model="content" id="content" rows="8"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                    placeholder="Write your note..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <select wire:model="category"
                    class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="">Select category</option>
                    <option value="General">General</option>
                    <option value="Important">Important</option>
                    <option value="Idea">Idea</option>
                    <option value="Tutorial">Tutorial</option>
                    <option value="Todo">Todo</option>
                </select>
                <label class="flex items-center gap-3 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium cursor-pointer">
                    <input type="checkbox" wire:model="is_pinned" class="w-4 h-4 accent-primary">
                    <span><i class="bx bx-pin mr-1"></i> Pin note</span>
                </label>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="Livewire.dispatch('close-modal', { id: 'note-form' })"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Save
                </button>
            </div>
        </form>
    </x-modal>

    @foreach ($notes as $note)
        <x-modal id="note-edit-{{ $note->id }}" title="Edit Note">
            <form wire:submit="update({{ $note->id }})" class="space-y-4">
                <x-input label="Title" name="edit_title" placeholder="Note title..." wire:model="edit_title" />
                <div>
                    <label for="edit_content_{{ $note->id }}" class="block text-sm font-semibold text-txt-primary mb-1.5">Content</label>
                    <textarea wire:model="edit_content" id="edit_content_{{ $note->id }}" rows="8"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                        placeholder="Write your note..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <select wire:model="edit_category"
                        class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                        <option value="">Select category</option>
                        <option value="General">General</option>
                        <option value="Important">Important</option>
                        <option value="Idea">Idea</option>
                        <option value="Tutorial">Tutorial</option>
                        <option value="Todo">Todo</option>
                    </select>
                    <label class="flex items-center gap-3 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium cursor-pointer">
                        <input type="checkbox" wire:model="edit_is_pinned" class="w-4 h-4 accent-primary">
                        <span><i class="bx bx-pin mr-1"></i> Pin note</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="Livewire.dispatch('close-modal', { id: 'note-edit-{{ $note->id }}' })"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Update
                    </button>
                </div>
            </form>
        </x-modal>
    @endforeach
@endsection
