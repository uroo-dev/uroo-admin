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

    @php
        $checklists = $checklists ?? collect();
    @endphp

    <div class="space-y-6">
        @forelse ($checklists as $checklist)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-button flex items-center justify-center flex-shrink-0 bg-gray-100">
                            <i class="bx bx-list-check text-txt-secondary text-[24px]"></i>
                        </div>
                        <div>
                            <h3 class="font-bold">{{ $checklist->title ?? 'Checklist' }}</h3>
                            <p class="text-xs text-txt-secondary mt-0.5">0 items</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <x-badge variant="default">Not Ready</x-badge>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="w-full h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-[#22C55E] rounded-full" style="width: 0%"></div>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    @foreach ($checklist->items ?? [] as $item)
                        <div class="flex items-start gap-3 px-3 py-2.5 rounded-button border-2 border-border-dark">
                            <input type="checkbox" {{ $item->is_checked ?? false ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 accent-[#22C55E] flex-shrink-0">
                            <span class="text-sm font-medium {{ $item->is_checked ?? false ? 'line-through text-txt-secondary' : '' }}">
                                {{ $item->name ?? 'Item' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-check-shield text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No checklists yet</h3>
                <p class="text-txt-secondary mt-2">Create your first deployment checklist</p>
            </div>
        @endforelse
    </div>

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
