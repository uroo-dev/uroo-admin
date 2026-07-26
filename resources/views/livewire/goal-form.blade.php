<div>
    <form wire:submit="save" class="space-y-4">
        <x-input label="Nama Goal" name="name" wire:model="name" placeholder="MacBook Pro M4" />
        <x-input label="Target Amount (Rp)" name="targetAmount" type="number" wire:model="targetAmount" placeholder="35000000" />
        <div class="grid grid-cols-2 gap-4">
            <x-input label="Icon (BoxIcons class)" name="icon" wire:model="icon" placeholder="bx bxs-laptop" />
            <x-input label="Color (hex)" name="color" wire:model="color" placeholder="#4F46E5" />
        </div>
        <x-input label="Deadline" name="deadline" type="date" wire:model="deadline" />
        <div>
            <label class="block text-sm font-semibold mb-1.5">Catatan</label>
            <textarea wire:model="notes" rows="2" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none"></textarea>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" x-on:click="open = false" class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Batal</button>
            <button type="submit" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Simpan</button>
        </div>
    </form>
</div>