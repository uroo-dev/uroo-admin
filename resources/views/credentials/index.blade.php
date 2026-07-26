@extends('layouts.app')

@section('title', 'Credential Vault')
@section('page-title', 'Credential Vault')

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard">
                    <i class="bx bx-lock-alt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['total'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Credentials</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard">
                    <i class="bx bx-star text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['favorites'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Favorites</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard">
                    <i class="bx bx-time text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['expiring'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Expiring Soon</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-acc rounded-button flex items-center justify-center shadow-hard">
                    <i class="bx bx-category text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ count($stats['byType']) }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Categories</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Actions --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex gap-3 flex-wrap">
                <select wire:model.live="type"
                    class="px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="">All Types</option>
                    @foreach ($types as $t)
                        <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
                <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium">
                    <input type="checkbox" wire:model.live="showFavorites" class="w-4 h-4 accent-primary">
                    <span>Favorites Only</span>
                </label>
            </div>
            <div class="flex gap-3">
                <div class="relative">
                    <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search credentials..."
                        class="w-64 pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                </div>
                <button onclick="Livewire.dispatch('open-modal', { id: 'credential-form' })"
                    class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
                    <i class="bx bx-plus text-lg"></i>
                    Add Credential
                </button>
            </div>
        </div>
    </div>

    {{-- Credential Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($credentials as $credential)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-primary/10 rounded-button flex items-center justify-center">
                            <i class="bx {{ $credential->type === 'api_key' ? 'bx-key' : ($credential->type === 'ssh' ? 'bx-terminal' : ($credential->type === 'database' ? 'bx-data' : ($credential->type === 'email' ? 'bx-envelope' : ($credential->type === 'ftp' ? 'bx-upload' : ($credential->type === 'cloud' ? 'bx-cloud' : ($credential->type === 'cpanel' ? 'bx-crown' : 'bx-lock-alt')))))) }} text-primary text-[24px]"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm">{{ $credential->label }}</h3>
                            <p class="text-xs text-txt-secondary">{{ ucfirst($credential->type) }}</p>
                        </div>
                    </div>
                    <button wire:click="toggleFavorite({{ $credential->id }})"
                        class="text-xl {{ $credential->is_favorite ? 'text-[#F59E0B]' : 'text-txt-secondary' }} hover:text-[#F59E0B] transition-colors">
                        <i class="bx {{ $credential->is_favorite ? 'bxs-star' : 'bx-star' }}"></i>
                    </button>
                </div>

                <div class="space-y-2 text-sm">
                    @if ($credential->provider)
                        <div class="flex justify-between">
                            <span class="text-txt-secondary">Provider</span>
                            <span class="font-semibold">{{ $credential->provider }}</span>
                        </div>
                    @endif
                    @if ($credential->domain)
                        <div class="flex justify-between">
                            <span class="text-txt-secondary">Domain</span>
                            <span class="font-semibold">{{ $credential->domain }}</span>
                        </div>
                    @endif
                    @if ($credential->username)
                        <div class="flex justify-between">
                            <span class="text-txt-secondary">Username</span>
                            <span class="font-semibold">{{ $credential->username }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex gap-2 mt-4 pt-4 border-t-4 border-border-dark">
                    <button onclick="Livewire.dispatch('open-modal', { id: 'credential-show-{{ $credential->id }}' })"
                        class="flex-1 px-3 py-2 bg-primary text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        <i class="bx bx-show"></i> View
                    </button>
                    <button wire:click="$dispatch('swal:confirm', { event: 'delete-credential-{{ $credential->id }}', title: 'Hapus credential ini?', text: '{{ $credential->label }} akan dihapus permanen.', confirmText: 'Ya, hapus!' })"
                        class="px-3 py-2 bg-danger text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-lock-alt text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">Belum ada credential</h3>
                <p class="text-txt-secondary mt-2">Tambahkan credential pertamamu</p>
                <button onclick="Livewire.dispatch('open-modal', { id: 'credential-form' })"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + Tambah Credential
                </button>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($credentials->hasPages())
        <div class="mt-6">
            {{ $credentials->links() }}
        </div>
    @endif

    {{-- Credential Form Modal --}}
    <x-modal id="credential-form" title="Tambah Credential">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Label" name="label" placeholder="My Database" wire:model="label" />
                <select wire:model="credentialType"
                    class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="">Pilih tipe</option>
                    @foreach ($types as $t)
                        <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <x-input label="Provider" name="provider" placeholder="DigitalOcean" wire:model="provider" />
            <x-input label="Domain / Host IP" name="domain" placeholder="example.com" wire:model="domain" />
            <x-input label="Username" name="username" placeholder="root" wire:model="username" />
            <x-input label="Password" name="password" type="password" wire:model="password" />
            <x-input label="Notes" name="notes" placeholder="Catatan..." wire:model="notes" />
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="Livewire.dispatch('close-modal', { id: 'credential-form' })"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Simpan
                </button>
            </div>
        </form>
    </x-modal>
@endsection