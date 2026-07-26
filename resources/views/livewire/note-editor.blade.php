<div>
    <form wire:submit="save" class="space-y-4">
        <x-input label="Judul" name="title" wire:model="title" placeholder="Judul note" />
        <div>
            <label class="block text-sm font-semibold mb-1.5">Konten</label>
            <textarea wire:model="content" rows="10" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none" placeholder="Tulis note disini... (Markdown supported)"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <x-input label="Kategori" name="category" wire:model="category" placeholder="AI Prompt, Laravel, dll" />
            <div>
                <label class="block text-sm font-semibold mb-1.5">Tags</label>
                <div class="flex gap-2">
                    <input wire:model="tagInput" wire:keydown.enter.prevent="addTag" placeholder="Tambah tag" class="flex-1 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <button type="button" wire:click="addTag" class="px-4 py-3 bg-primary text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm">+</button>
                </div>
                @if (count($tags))
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($tags as $index => $tag)
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10">
                                {{ $tag }}
                                <button type="button" wire:click="removeTag({{ $index }})" class="text-danger hover:text-red-700">&times;</button>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="isPinned" class="w-4 h-4 accent-primary">
                <span class="text-sm font-medium">Pin</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="isFavorite" class="w-4 h-4 accent-primary">
                <span class="text-sm font-medium">Favorite</span>
            </label>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" x-on:click="open = false" class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Batal</button>
            <button type="submit" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Simpan</button>
        </div>
    </form>
</div>