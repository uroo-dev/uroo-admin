@extends('layouts.app')

@section('title', 'Quality Control')
@section('page-title', 'Quality Control')

@section('content')
    <div class="flex items-center justify-between gap-4 mb-8">
        <div>
            <p class="text-txt-secondary text-sm">Manage your deployment checklists and quality assurance</p>
        </div>
        <button onclick="Livewire.dispatch('open-modal', { id: 'checklist-form' })"
            class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
            <i class="bx bx-plus text-lg"></i>
            New Checklist
        </button>
    </div>

    <div class="space-y-6">
        @forelse ($checklists as $checklist)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-button flex items-center justify-center flex-shrink-0
                            {{ $checklist->progress >= 100 ? 'bg-[#22C55E]/10' : ($checklist->progress >= 50 ? 'bg-[#F59E0B]/10' : 'bg-gray-100') }}">
                            <i class="bx {{ $checklist->progress >= 100 ? 'bxs-check-circle text-[#22C55E]' : 'bx-list-check text-txt-secondary' }} text-[24px]"></i>
                        </div>
                        <div>
                            <h3 class="font-bold">{{ $checklist->title }}</h3>
                            <p class="text-xs text-txt-secondary mt-0.5">{{ $checklist->items->count() }} items</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if ($checklist->progress >= 100)
                            <x-badge variant="success">Ready to Deploy</x-badge>
                        @elseif ($checklist->progress >= 50)
                            <x-badge variant="warning">In Progress</x-badge>
                        @else
                            <x-badge variant="danger">Not Ready</x-badge>
                        @endif
                        <button wire:click="edit({{ $checklist->id }})"
                            class="px-3 py-1.5 bg-surface text-txt-primary font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                            <i class="bx bx-pencil"></i>
                        </button>
                        <button wire:click="$dispatch('swal:confirm', { event: 'delete-checklist-{{ $checklist->id }}', title: 'Delete this checklist?', text: '{{ addslashes($checklist->title) }} will be removed.', confirmText: 'Yes, delete!' })"
                            class="px-3 py-1.5 bg-danger text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4" x-data="{ progress: {{ $checklist->progress }} }">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-semibold">Progress</span>
                        <span class="text-xs font-extrabold" x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-[#22C55E] rounded-full transition-all duration-500 ease-out"
                            :style="'width: ' + progress + '%'"
                            style="width: {{ $checklist->progress }}%">
                        </div>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    @foreach ($checklist->items as $item)
                        <label wire:key="item-{{ $item->id }}"
                            class="flex items-start gap-3 px-3 py-2.5 rounded-button border-2 border-border-dark cursor-pointer transition-colors hover:bg-gray-50 {{ $item->is_checked ? 'bg-[#22C55E]/5 border-[#22C55E]' : '' }}">
                            <input type="checkbox" wire:click="toggleItem({{ $item->id }})"
                                {{ $item->is_checked ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 accent-[#22C55E] flex-shrink-0">
                            <span class="text-sm font-medium {{ $item->is_checked ? 'line-through text-txt-secondary' : '' }}">
                                {{ $item->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                @if ($checklist->notes)
                    <div class="flex items-start gap-2 px-3 py-2.5 bg-gray-50 rounded-button border-2 border-border-dark text-sm">
                        <i class="bx bx-notepad text-txt-secondary mt-0.5"></i>
                        <p class="text-txt-secondary">{{ $checklist->notes }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-check-shield text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No checklists yet</h3>
                <p class="text-txt-secondary mt-2">Create your first deployment checklist</p>
                <button onclick="Livewire.dispatch('open-modal', { id: 'checklist-form' })"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + New Checklist
                </button>
            </div>
        @endforelse
    </div>

    @if ($checklists->hasPages())
        <div class="mt-6">
            {{ $checklists->links() }}
        </div>
    @endif

    <x-modal id="checklist-form" title="New Checklist">
        <form wire:submit="save" class="space-y-4">
            <x-input label="Checklist Title" name="title" placeholder="Deployment Checklist v1.0" wire:model="title" />
            <div>
                <label class="block text-sm font-semibold text-txt-primary mb-1.5">Checklist Items</label>
                <div class="space-y-2">
                    @foreach ($items as $index => $item)
                        <div class="flex items-center gap-2" wire:key="item-{{ $index }}">
                            <input type="text" wire:model="items.{{ $index }}.name"
                                class="flex-1 px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none"
                                placeholder="Add item...">
                            <button type="button" wire:click="removeItem({{ $index }})"
                                class="p-2 text-danger hover:bg-danger/10 rounded-button transition-colors">
                                <i class="bx bx-x text-xl"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" wire:click="addItem"
                    class="mt-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors flex items-center gap-1">
                    <i class="bx bx-plus"></i> Add item
                </button>
            </div>
            <div>
                <label for="notes" class="block text-sm font-semibold text-txt-primary mb-1.5">Notes (optional)</label>
                <textarea wire:model="notes" id="notes" rows="3"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                    placeholder="Additional notes..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="Livewire.dispatch('close-modal', { id: 'checklist-form' })"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Save Checklist
                </button>
            </div>
        </form>
    </x-modal>

    <x-modal id="checklist-edit" title="Edit Checklist">
        <form wire:submit="update" class="space-y-4">
            <x-input label="Checklist Title" name="edit_title" placeholder="Deployment Checklist v1.0" wire:model="edit_title" />
            <div>
                <label class="block text-sm font-semibold text-txt-primary mb-1.5">Checklist Items</label>
                <div class="space-y-2">
                    @foreach ($edit_items as $index => $item)
                        <div class="flex items-center gap-2" wire:key="edit-item-{{ $index }}">
                            <input type="text" wire:model="edit_items.{{ $index }}.name"
                                class="flex-1 px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none"
                                placeholder="Add item...">
                            <button type="button" wire:click="removeEditItem({{ $index }})"
                                class="p-2 text-danger hover:bg-danger/10 rounded-button transition-colors">
                                <i class="bx bx-x text-xl"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" wire:click="addEditItem"
                    class="mt-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors flex items-center gap-1">
                    <i class="bx bx-plus"></i> Add item
                </button>
            </div>
            <div>
                <label for="edit_notes" class="block text-sm font-semibold text-txt-primary mb-1.5">Notes (optional)</label>
                <textarea wire:model="edit_notes" id="edit_notes" rows="3"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                    placeholder="Additional notes..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="Livewire.dispatch('close-modal', { id: 'checklist-edit' })"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Update Checklist
                </button>
            </div>
        </form>
    </x-modal>
@endsection
