<div>
    <form wire:submit="save" class="space-y-4">
        <x-input label="Nama Aplikasi" name="name" wire:model="name" placeholder="Nama ide aplikasi" />
        <x-input label="Tagline" name="tagline" wire:model="tagline" placeholder="Deskripsi singkat" />
        <div>
            <label class="block text-sm font-semibold mb-1.5">Deskripsi</label>
            <textarea wire:model="description" rows="4" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none" placeholder="Jelaskan ide aplikasimu..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1.5">Fitur (satu per baris)</label>
            <textarea wire:model="features" rows="3" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none" placeholder="Fitur 1&#10;Fitur 2"></textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1.5">Tech Stack (satu per baris)</label>
            <textarea wire:model="techStack" rows="3" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none" placeholder="Laravel&#10;React&#10;MySQL"></textarea>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1.5">Platform</label>
                <select wire:model="platform" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="web">Web</option>
                    <option value="mobile">Mobile</option>
                    <option value="desktop">Desktop</option>
                    <option value="hybrid">Hybrid</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Status</label>
                <select wire:model="status" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="draft">Draft</option>
                    <option value="research">Research</option>
                    <option value="development">Development</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Priority</label>
                <select wire:model="priority" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
        </div>
        <x-input label="Tags (pisahkan dengan koma)" name="tags" wire:model="tags" placeholder="ai, productivity, tools" />
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