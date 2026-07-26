<div>
    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <x-input label="Nama" name="name" wire:model="name" placeholder="Nama client" />
            <x-input label="Email" name="email" type="email" wire:model="email" placeholder="client@email.com" />
        </div>
        <div class="grid grid-cols-2 gap-4">
            <x-input label="Telepon" name="phone" wire:model="phone" placeholder="08123456789" />
            <x-input label="WhatsApp" name="whatsapp" wire:model="whatsapp" placeholder="628123456789" />
        </div>
        <x-input label="Perusahaan" name="company" wire:model="company" placeholder="PT. Contoh" />
        <x-input label="Alamat" name="address" wire:model="address" placeholder="Jl. Merdeka No. 1" />
        <x-input label="Website" name="website" wire:model="website" placeholder="https://contoh.com" />
        <div>
            <label class="block text-sm font-semibold mb-1.5">Status</label>
            <select wire:model="status" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1.5">Catatan</label>
            <textarea wire:model="notes" rows="3" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none" placeholder="Catatan..."></textarea>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" x-on:click="open = false" class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Batal</button>
            <button type="submit" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Simpan</button>
        </div>
    </form>
</div>