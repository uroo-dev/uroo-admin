@extends('layouts.app')

@section('title', 'Credential Vault')
@section('page-title', 'Credential Vault')

@section('content')
<div x-data="{ addModalOpen: false }">
@php
    $typeIcons = [
        'api_key' => 'bx-key',
        'ssh' => 'bx-terminal',
        'database' => 'bx-data',
        'email' => 'bx-envelope',
        'ftp' => 'bx-upload',
        'cloud' => 'bx-cloud',
        'cpanel' => 'bx-crown',
        'hosting' => 'bx-server',
        'vps' => 'bx-desktop',
    ];
@endphp

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

{{-- Toolbar --}}
<div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
    <form method="GET" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex gap-3 flex-wrap items-center">
            <select name="type" class="px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Types</option>
                @foreach ($types as $t)
                    <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium">
                <input type="checkbox" name="favorites" value="1" {{ $showFavorites ? 'checked' : '' }}>
                <span>Favorites Only</span>
            </label>
            <div class="relative">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search credentials..." class="w-64 pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Filter</button>
        </div>
        <button type="button" @click="addModalOpen = true" class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 self-start">
            <i class="bx bx-plus text-lg"></i>
            Add Credential
        </button>
    </form>
</div>

{{-- Credential Cards Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($credentials as $credential)
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover" x-data="{ showDetail: false }">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-primary/10 rounded-button flex items-center justify-center">
                        <i class="bx {{ $typeIcons[$credential->type] ?? 'bx-lock-alt' }} text-primary text-[24px]"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">{{ $credential->label }}</h3>
                        <span class="text-xs font-medium bg-primary/10 text-primary px-2 py-0.5 rounded-full">{{ ucfirst($credential->type) }}</span>
                    </div>
                </div>
            </div>

            <div x-show="!showDetail">
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
            </div>

            <div x-show="showDetail" x-transition>
                <div class="space-y-2 text-sm">
                    @if ($credential->provider)
                        <div class="flex justify-between"><span class="text-txt-secondary">Provider</span><span class="font-semibold">{{ $credential->provider }}</span></div>
                    @endif
                    @if ($credential->domain)
                        <div class="flex justify-between"><span class="text-txt-secondary">Domain</span><span class="font-semibold">{{ $credential->domain }}</span></div>
                    @endif
                    @if ($credential->username)
                        <div class="flex justify-between"><span class="text-txt-secondary">Username</span><span class="font-semibold">{{ $credential->username }}</span></div>
                    @endif
                    @if ($credential->notes)
                        <div class="flex justify-between"><span class="text-txt-secondary">Notes</span><span class="font-semibold">{{ $credential->notes }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-txt-secondary">Favorite</span><span class="font-semibold">{{ $credential->is_favorite ? 'Yes' : 'No' }}</span></div>
                </div>
            </div>

            <div class="flex gap-2 mt-4 pt-4 border-t-4 border-border-dark">
                <button type="button" @click="showDetail = !showDetail" class="flex-1 px-3 py-2 bg-primary text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    <i class="bx bx-show"></i> View
                </button>
                <form method="POST" action="{{ route('credentials.destroy', $credential) }}" onsubmit="return confirm('Are you sure you want to delete this credential?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-danger text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        <i class="bx bx-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-lock-alt text-6xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">Belum ada credential</h3>
            <p class="text-txt-secondary mt-2">Tambahkan credential pertamamu</p>
            <button type="button" @click="addModalOpen = true" class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                + Tambah Credential
            </button>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
{{ $credentials->links() }}

{{-- Add Credential Modal --}}
<div x-show="addModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
    <div x-show="addModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0" @click.outside="addModalOpen = false" class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg animate-scale-in" style="display: none;">
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
            <h3 class="text-lg font-extrabold">Add Credential</h3>
            <button type="button" @click="addModalOpen = false" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('credentials.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-txt-secondary mb-1">Label</label>
                <input type="text" name="label" required class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-txt-secondary mb-1">Type</label>
                <select name="type" required class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="">Pilih tipe</option>
                    @foreach ($types as $t)
                        <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-txt-secondary mb-1">Provider</label>
                    <input type="text" name="provider" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-txt-secondary mb-1">Domain</label>
                    <input type="text" name="domain" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-txt-secondary mb-1">Username</label>
                <input type="text" name="username" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-txt-secondary mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-txt-secondary mb-1">Notes</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="addModalOpen = false" class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Batal</button>
                <button type="submit" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Simpan</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection