@extends('layouts.app')

@section('title', 'Credential Vault')
@section('page-title', 'Credential Vault')

@section('content')
@php
    $typeIcons = [
        'api_key'  => ['icon' => 'bx-key',      'color' => 'bg-yellow-400'],
        'ssh'      => ['icon' => 'bx-terminal',  'color' => 'bg-[#111827]'],
        'database' => ['icon' => 'bx-data',      'color' => 'bg-secondary'],
        'email'    => ['icon' => 'bx-envelope',  'color' => 'bg-pink-400'],
        'ftp'      => ['icon' => 'bx-upload',    'color' => 'bg-cyan-400'],
        'cloud'    => ['icon' => 'bx-cloud',     'color' => 'bg-[#22C55E]'],
        'cpanel'   => ['icon' => 'bx-crown',     'color' => 'bg-purple-500'],
        'hosting'  => ['icon' => 'bx-server',    'color' => 'bg-primary'],
        'vps'      => ['icon' => 'bx-desktop',   'color' => 'bg-[#F59E0B]'],
    ];
@endphp

{{-- Flash Messages --}}
@if (session('success'))
    <div id="flash-success" class="mb-6 flex items-start gap-3 bg-[#22C55E]/10 border-4 border-[#22C55E] rounded-card px-5 py-4 shadow-hard">
        <i class="bx bx-check-circle text-[#22C55E] text-2xl flex-shrink-0 mt-0.5"></i>
        <p class="text-sm font-bold text-[#166534] flex-1">{{ session('success') }}</p>
        <button onclick="document.getElementById('flash-success').remove()" class="text-[#22C55E] hover:opacity-70 text-xl leading-none">&times;</button>
    </div>
@endif
@if (session('error'))
    <div id="flash-error" class="mb-6 flex items-start gap-3 bg-red-50 border-4 border-red-500 rounded-card px-5 py-4 shadow-hard">
        <i class="bx bx-error-circle text-red-500 text-2xl flex-shrink-0 mt-0.5"></i>
        <p class="text-sm font-bold text-red-700 flex-1">{{ session('error') }}</p>
        <button onclick="document.getElementById('flash-error').remove()" class="text-red-500 hover:opacity-70 text-xl leading-none">&times;</button>
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
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
            <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                <i class="bx bxs-star text-white text-[28px]"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold">{{ $stats['favorites'] }}</p>
                <p class="text-sm font-medium text-txt-secondary">Favorites</p>
            </div>
        </div>
    </div>
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-purple-500 rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
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
<div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 mb-8">
    <form method="GET" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search credentials..."
                    class="pl-10 pr-4 py-2.5 w-56 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
            </div>
            <select name="type" onchange="this.form.submit()"
                class="px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                <option value="">All Types</option>
                @foreach ($types as $t)
                    <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $t)) }}
                    </option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium select-none">
                <input type="checkbox" name="favorites" value="1" {{ $showFavorites ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 accent-primary">
                <i class="bx bxs-star text-[#F59E0B]"></i> Favorites Only
            </label>
            @if ($search)
                <a href="{{ route('credentials.index') }}" class="px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-secondary hover:text-danger transition-colors flex items-center gap-1.5">
                    <i class="bx bx-x text-base"></i> Clear
                </a>
            @endif
        </div>
        <button type="button" onclick="openAddModal()"
            class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 self-start sm:self-auto whitespace-nowrap">
            <i class="bx bx-plus text-lg"></i> Add Credential
        </button>
    </form>
</div>


{{-- Cards Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse ($credentials as $credential)
        @php
            $ti = $typeIcons[$credential->type] ?? ['icon' => 'bx-lock-alt', 'color' => 'bg-primary'];
            $hasPassword = !empty($credential->password_encrypted);
        @endphp
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard flex flex-col gap-0 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">

            {{-- Card Header --}}
            <div class="flex items-center gap-3 p-5 border-b-4 border-border-dark">
                <div class="w-12 h-12 {{ $ti['color'] }} rounded-button flex items-center justify-center shadow-hard-sm flex-shrink-0">
                    <i class="bx {{ $ti['icon'] }} text-white text-[22px]"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-extrabold text-sm leading-tight truncate">{{ $credential->label }}</h3>
                    <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full border-2 border-border-dark bg-primary/10 text-primary mt-0.5">
                        {{ ucfirst(str_replace('_', ' ', $credential->type)) }}
                    </span>
                </div>
                {{-- Favorite Toggle --}}
                <form method="POST" action="{{ route('credentials.update', $credential) }}" class="flex-shrink-0">
                    @csrf @method('PUT')
                    <input type="hidden" name="label" value="{{ $credential->label }}">
                    <input type="hidden" name="type" value="{{ $credential->type }}">
                    <input type="hidden" name="username" value="{{ $credential->username }}">
                    <input type="hidden" name="is_favorite" value="{{ $credential->is_favorite ? '0' : '1' }}">
                    <button type="submit" title="{{ $credential->is_favorite ? 'Hapus dari favorites' : 'Tambah ke favorites' }}"
                        class="w-9 h-9 flex items-center justify-center rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200">
                        <i class="bx {{ $credential->is_favorite ? 'bxs-star text-[#F59E0B]' : 'bx-star text-txt-secondary' }} text-xl"></i>
                    </button>
                </form>
            </div>

            {{-- Card Body --}}
            <div class="p-5 flex-1 space-y-3">
                @if ($credential->username)
                    <div class="flex items-center justify-between gap-2 group">
                        <div class="flex items-center gap-2 text-txt-secondary text-xs font-semibold min-w-0">
                            <i class="bx bx-user text-sm flex-shrink-0"></i>
                            <span class="truncate font-mono text-xs text-txt-primary bg-bgmain border-2 border-border-dark px-2 py-1 rounded-lg">{{ $credential->username }}</span>
                        </div>
                        <button type="button"
                            onclick="copyText('{{ addslashes($credential->username) }}', 'Username')"
                            class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg border-2 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 opacity-60 group-hover:opacity-100"
                            title="Copy username">
                            <i class="bx bx-copy text-sm"></i>
                        </button>
                    </div>
                @endif

                @if ($hasPassword)
                    <div class="flex items-center justify-between gap-2 group">
                        <div class="flex items-center gap-2 text-txt-secondary min-w-0">
                            <i class="bx bx-lock-alt text-sm flex-shrink-0 text-txt-secondary"></i>
                            <span class="font-mono text-xs text-txt-primary bg-bgmain border-2 border-border-dark px-2 py-1 rounded-lg tracking-widest">••••••••••••</span>
                        </div>
                        <button type="button"
                            data-copy-pwd="{{ $credential->password }}"
                            onclick="copyText(this.getAttribute('data-copy-pwd'), 'Password')"
                            class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg border-2 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 opacity-60 group-hover:opacity-100"
                            title="Copy password">
                            <i class="bx bx-copy text-sm"></i>
                        </button>
                    </div>
                @endif

                @if (!$credential->username && !$hasPassword)
                    <p class="text-xs text-txt-secondary italic">No credentials stored</p>
                @endif
            </div>

            {{-- Card Actions --}}
            <div class="flex items-center gap-2 p-4 border-t-4 border-border-dark mt-auto">
                {{-- View Button --}}
                <button type="button"
                    data-id="{{ $credential->id }}"
                    data-label="{{ $credential->label }}"
                    data-type="{{ $credential->type }}"
                    data-username="{{ $credential->username ?? '' }}"
                    data-password="{{ $credential->password ?? '' }}"
                    onclick="openViewModalFromEl(this)"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-txt-secondary hover:text-primary font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out">
                    <i class="bx bx-show text-base"></i> View
                </button>

                {{-- Edit Button --}}
                <button type="button"
                    data-id="{{ $credential->id }}"
                    data-label="{{ $credential->label }}"
                    data-type="{{ $credential->type }}"
                    data-username="{{ $credential->username ?? '' }}"
                    onclick="openEditModalFromEl(this)"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-txt-secondary hover:text-[#F59E0B] font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out">
                    <i class="bx bx-edit text-base"></i> Edit
                </button>

                {{-- Delete Button --}}
                <form id="del-cred-{{ $credential->id }}" method="POST" action="{{ route('credentials.destroy', $credential) }}" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="button"
                        onclick="deleteCredential('del-cred-{{ $credential->id }}', {{ json_encode($credential->label) }})"
                        class="w-full flex items-center justify-center gap-1.5 px-3 py-2 text-danger font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 hover:bg-red-50 transition-all duration-200 ease-out">
                        <i class="bx bx-trash text-base"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <div class="w-24 h-24 bg-primary/10 border-4 border-border-dark rounded-card flex items-center justify-center mb-5 shadow-hard">
                <i class="bx bx-lock-alt text-5xl text-primary"></i>
            </div>
            <h3 class="text-2xl font-extrabold mb-2">Vault Kosong</h3>
            <p class="text-txt-secondary text-sm mb-6 text-center max-w-xs">Simpan password, API key, SSH key, dan credential lainnya dengan aman dan terenkripsi.</p>
            <button type="button" onclick="openAddModal()"
                class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
                <i class="bx bx-plus text-lg"></i> Tambah Credential Pertama
            </button>
        </div>
    @endforelse
</div>

@if ($credentials->hasPages())
    <div class="mt-8">{{ $credentials->links() }}</div>
@endif


{{-- ===== VIEW DETAIL MODAL ===== --}}
<div id="view-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeViewModal()">
    <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-md" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
            <div class="flex items-center gap-3">
                <div id="view-icon-wrap" class="w-10 h-10 bg-primary rounded-button flex items-center justify-center shadow-hard-sm">
                    <i id="view-icon" class="bx bx-lock-alt text-white text-xl"></i>
                </div>
                <h3 class="text-lg font-extrabold">Detail Credential</h3>
            </div>
            <button onclick="closeViewModal()" class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-border-dark hover:bg-red-50 hover:text-danger transition-colors text-txt-secondary text-xl font-bold">
                &times;
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5">
            {{-- Label --}}
            <div class="bg-bgmain border-4 border-border-dark rounded-card p-4">
                <p class="text-xs font-bold text-txt-secondary uppercase tracking-wide mb-1.5">Label</p>
                <div class="flex items-center justify-between gap-3">
                    <span id="view-label" class="font-extrabold text-base"></span>
                    <button type="button" onclick="copyFromSpan('view-label', 'Label')"
                        class="flex-shrink-0 px-2 py-1 rounded-lg border-2 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all text-xs font-bold flex items-center gap-1 text-txt-secondary hover:text-primary">
                        <i class="bx bx-copy text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Type --}}
            <div class="bg-bgmain border-4 border-border-dark rounded-card p-4">
                <p class="text-xs font-bold text-txt-secondary uppercase tracking-wide mb-1.5">Type</p>
                <span id="view-type" class="font-bold text-sm px-3 py-1 rounded-full border-2 border-border-dark bg-primary/10 text-primary"></span>
            </div>

            {{-- Username --}}
            <div class="bg-bgmain border-4 border-border-dark rounded-card p-4">
                <p class="text-xs font-bold text-txt-secondary uppercase tracking-wide mb-1.5">Username / Email</p>
                <div class="flex items-center justify-between gap-3">
                    <span id="view-username" class="font-mono text-sm text-txt-primary"></span>
                    <button type="button" onclick="copyFromSpan('view-username', 'Username')"
                        class="flex-shrink-0 px-2 py-1 rounded-lg border-2 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all text-xs font-bold flex items-center gap-1 text-txt-secondary hover:text-primary">
                        <i class="bx bx-copy text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Password --}}
            <div class="bg-bgmain border-4 border-border-dark rounded-card p-4">
                <p class="text-xs font-bold text-txt-secondary uppercase tracking-wide mb-1.5">Password</p>
                <div class="flex items-center justify-between gap-3">
                    <span id="view-password" class="font-mono text-sm text-txt-primary flex-1 break-all"></span>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button type="button" onclick="toggleViewPassword()"
                            class="px-2 py-1 rounded-lg border-2 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all text-xs font-bold text-txt-secondary hover:text-primary">
                            <i id="view-pwd-eye" class="bx bx-show text-sm"></i>
                        </button>
                        <button type="button" onclick="copyFromSpan('view-password-raw', 'Password')"
                            class="px-2 py-1 rounded-lg border-2 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all text-xs font-bold text-txt-secondary hover:text-primary">
                            <i class="bx bx-copy text-sm"></i>
                        </button>
                    </div>
                </div>
                {{-- Hidden span for actual password value --}}
                <span id="view-password-raw" class="hidden"></span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-3 px-6 pb-6">
            <button type="button" onclick="closeViewModal()"
                class="px-5 py-2.5 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out">
                Tutup
            </button>
        </div>
    </div>
</div>


{{-- ===== ADD / EDIT FORM MODAL ===== --}}
<div id="form-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeFormModal()">
    <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">

        {{-- Sticky Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark sticky top-0 bg-surface z-10">
            <div class="flex items-center gap-3">
                <div id="form-modal-icon" class="w-10 h-10 bg-primary rounded-button flex items-center justify-center shadow-hard-sm">
                    <i class="bx bx-plus text-white text-xl"></i>
                </div>
                <h3 id="form-modal-title" class="text-lg font-extrabold">Add Credential</h3>
            </div>
            <button onclick="closeFormModal()" class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-border-dark hover:bg-red-50 hover:text-danger transition-colors text-txt-secondary text-xl font-bold">
                &times;
            </button>
        </div>

        <form id="credential-form" method="POST" action="{{ route('credentials.store') }}" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">

            {{-- Label --}}
            <div>
                <label class="block text-sm font-bold text-txt-primary mb-2">
                    Label <span class="text-danger">*</span>
                </label>
                <input type="text" name="label" id="f-label" required
                    placeholder="e.g. Production Server, GitHub Token, cPanel Hosting"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>

            {{-- Type --}}
            <div>
                <label class="block text-sm font-bold text-txt-primary mb-2">
                    Type <span class="text-danger">*</span>
                </label>
                <input type="text" name="type" id="f-type" required
                    placeholder="e.g. hosting, vps, ssh, database, api_key, email"
                    list="type-suggestions"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                <datalist id="type-suggestions">
                    @foreach ($types as $t)
                        <option value="{{ $t }}">
                    @endforeach
                </datalist>
                <p class="text-xs text-txt-secondary mt-1">Ketik bebas atau pilih dari saran: hosting, vps, ssh, database, cpanel, cloud, ftp, api_key, email</p>
            </div>

            {{-- Username --}}
            <div>
                <label class="block text-sm font-bold text-txt-primary mb-2">Username / Email</label>
                <div class="relative">
                    <i class="bx bx-user absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                    <input type="text" name="username" id="f-username"
                        placeholder="username atau email"
                        class="w-full pl-10 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-bold text-txt-primary mb-2">Password</label>
                <div class="relative">
                    <i class="bx bx-lock-alt absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                    <input type="password" name="password" id="f-password"
                        placeholder="Kosongkan untuk auto-generate"
                        class="w-full pl-10 pr-24 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors font-mono">
                    {{-- Toggle show/hide --}}
                    <button type="button" onclick="toggleFieldPassword('f-password', 'f-pwd-eye')"
                        class="absolute right-12 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg border-2 border-border-dark bg-surface hover:-translate-y-0.5 transition-all text-txt-secondary hover:text-primary"
                        title="Show/Hide password">
                        <i id="f-pwd-eye" class="bx bx-show text-base"></i>
                    </button>
                    {{-- Generate --}}
                    <button type="button" onclick="generatePassword()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg border-2 border-border-dark bg-[#22C55E] shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all text-white"
                        title="Generate password otomatis">
                        <i class="bx bx-refresh text-base"></i>
                    </button>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-xs text-txt-secondary">Kosongkan untuk auto-generate 24 karakter strong password</p>
                    <button type="button" onclick="generatePassword()"
                        class="text-xs font-bold text-[#22C55E] hover:underline flex items-center gap-1">
                        <i class="bx bx-refresh text-sm"></i> Generate
                    </button>
                </div>

                {{-- Password strength indicator --}}
                <div id="pwd-strength-bar" class="mt-2 hidden">
                    <div class="flex gap-1 mb-1">
                        <div id="ps-1" class="h-1.5 flex-1 rounded-full bg-border-dark transition-colors duration-300"></div>
                        <div id="ps-2" class="h-1.5 flex-1 rounded-full bg-border-dark transition-colors duration-300"></div>
                        <div id="ps-3" class="h-1.5 flex-1 rounded-full bg-border-dark transition-colors duration-300"></div>
                        <div id="ps-4" class="h-1.5 flex-1 rounded-full bg-border-dark transition-colors duration-300"></div>
                    </div>
                    <p id="pwd-strength-label" class="text-xs font-semibold text-txt-secondary"></p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                <button type="button" onclick="closeFormModal()"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out">
                    Batal
                </button>
                <button type="submit" id="form-submit-btn"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
                    <i class="bx bx-save text-base"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ===== JAVASCRIPT ===== --}}
<script>
// ─── Data-attribute wrappers (safe for special chars in passwords) ─
function openViewModalFromEl(btn) {
    openViewModal(
        btn.dataset.id,
        btn.dataset.label,
        btn.dataset.type,
        btn.dataset.username,
        btn.dataset.password
    );
}

function openEditModalFromEl(btn) {
    openEditModal(
        btn.dataset.id,
        btn.dataset.label,
        btn.dataset.type,
        btn.dataset.username
    );
}

// ─── State ────────────────────────────────────────────────────────
var viewPasswordVisible = false;
var viewPasswordValue   = '';

// ─── Type icons map for modal header ─────────────────────────────
var typeColors = {
    'api_key':  'bg-yellow-400',
    'ssh':      'bg-[#111827]',
    'database': 'bg-secondary',
    'email':    'bg-pink-400',
    'ftp':      'bg-cyan-400',
    'cloud':    'bg-[#22C55E]',
    'cpanel':   'bg-purple-500',
    'hosting':  'bg-primary',
    'vps':      'bg-[#F59E0B]',
};
var typeIconMap = {
    'api_key':  'bx-key',
    'ssh':      'bx-terminal',
    'database': 'bx-data',
    'email':    'bx-envelope',
    'ftp':      'bx-upload',
    'cloud':    'bx-cloud',
    'cpanel':   'bx-crown',
    'hosting':  'bx-server',
    'vps':      'bx-desktop',
};

// ─── View Modal ───────────────────────────────────────────────────
function openViewModal(id, label, type, username, password) {
    viewPasswordValue   = password || '';
    viewPasswordVisible = false;

    document.getElementById('view-label').textContent    = label || '-';
    document.getElementById('view-type').textContent     = ucfirstType(type);
    document.getElementById('view-username').textContent = username || '-';
    document.getElementById('view-password-raw').textContent = password || '-';

    // Show masked password by default
    document.getElementById('view-password').textContent = password ? '••••••••••••' : '-';
    document.getElementById('view-pwd-eye').className = 'bx bx-show text-sm';

    // Set icon + color on header
    var iconEl  = document.getElementById('view-icon');
    var wrapEl  = document.getElementById('view-icon-wrap');
    iconEl.className  = 'bx ' + (typeIconMap[type] || 'bx-lock-alt') + ' text-white text-xl';
    wrapEl.className  = wrapEl.className.replace(/bg-\S+/, '');
    wrapEl.classList.add(typeColors[type] || 'bg-primary');

    document.getElementById('view-modal').classList.remove('hidden');
}

function closeViewModal() {
    document.getElementById('view-modal').classList.add('hidden');
}

function toggleViewPassword() {
    viewPasswordVisible = !viewPasswordVisible;
    var el  = document.getElementById('view-password');
    var eye = document.getElementById('view-pwd-eye');
    if (viewPasswordVisible) {
        el.textContent  = viewPasswordValue || '-';
        eye.className   = 'bx bx-hide text-sm';
    } else {
        el.textContent  = viewPasswordValue ? '••••••••••••' : '-';
        eye.className   = 'bx bx-show text-sm';
    }
}

// ─── Add / Edit Modal ─────────────────────────────────────────────
function openAddModal() {
    document.getElementById('form-modal-title').textContent = 'Add Credential';
    document.getElementById('form-modal-icon').className    = 'w-10 h-10 bg-primary rounded-button flex items-center justify-center shadow-hard-sm';
    document.getElementById('form-modal-icon').querySelector('i').className = 'bx bx-plus text-white text-xl';
    document.getElementById('credential-form').action = '{{ route('credentials.store') }}';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('form-submit-btn').innerHTML = '<i class="bx bx-save text-base"></i> Simpan';
    document.getElementById('credential-form').reset();
    document.getElementById('f-password').type = 'password';
    document.getElementById('f-pwd-eye').className = 'bx bx-show text-base';
    document.getElementById('pwd-strength-bar').classList.add('hidden');
    document.getElementById('form-modal').classList.remove('hidden');
}

function openEditModal(id, label, type, username) {
    document.getElementById('form-modal-title').textContent = 'Edit Credential';
    document.getElementById('form-modal-icon').className    = 'w-10 h-10 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard-sm';
    document.getElementById('form-modal-icon').querySelector('i').className = 'bx bx-edit text-white text-xl';
    document.getElementById('credential-form').action = '{{ url('credentials') }}/' + id;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('form-submit-btn').innerHTML = '<i class="bx bx-save text-base"></i> Update';

    document.getElementById('f-label').value    = label    || '';
    document.getElementById('f-type').value     = type     || '';
    document.getElementById('f-username').value = username || '';
    document.getElementById('f-password').value = '';
    document.getElementById('f-password').type  = 'password';
    document.getElementById('f-pwd-eye').className = 'bx bx-show text-base';
    document.getElementById('pwd-strength-bar').classList.add('hidden');
    document.getElementById('form-modal').classList.remove('hidden');
}

function closeFormModal() {
    document.getElementById('form-modal').classList.add('hidden');
}

// ─── Generate Password ────────────────────────────────────────────
function generatePassword() {
    var upper   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var lower   = 'abcdefghijklmnopqrstuvwxyz';
    var digits  = '0123456789';
    var special = '!@#$%^&*()-_=+[]{}';
    var all     = upper + lower + digits + special;

    // Guarantee at least 1 from each group
    var pwd = [
        upper[Math.floor(Math.random() * upper.length)],
        lower[Math.floor(Math.random() * lower.length)],
        digits[Math.floor(Math.random() * digits.length)],
        special[Math.floor(Math.random() * special.length)],
    ];
    for (var i = 4; i < 24; i++) {
        pwd.push(all[Math.floor(Math.random() * all.length)]);
    }
    // Shuffle
    pwd = pwd.sort(function() { return Math.random() - 0.5; });
    var result = pwd.join('');

    var input = document.getElementById('f-password');
    input.value = result;
    input.type  = 'text'; // show it after generate
    document.getElementById('f-pwd-eye').className = 'bx bx-hide text-base';

    updateStrengthBar(result);

    SwalToast.fire({
        icon: 'success',
        title: 'Password Generated!',
        html: '<code style="font-size:11px;word-break:break-all;font-family:monospace;">' + result + '</code>',
        timer: 3500,
    });
}

// ─── Password Strength ────────────────────────────────────────────
function updateStrengthBar(pwd) {
    var bar = document.getElementById('pwd-strength-bar');
    if (!pwd) { bar.classList.add('hidden'); return; }
    bar.classList.remove('hidden');

    var score = 0;
    if (pwd.length >= 8)  score++;
    if (pwd.length >= 16) score++;
    if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) score++;
    if (/[0-9]/.test(pwd)) score++;
    if (/[^A-Za-z0-9]/.test(pwd)) score++;
    // clamp to 4
    score = Math.min(score, 4);

    var colors  = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-[#22C55E]'];
    var labels  = ['Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
    for (var i = 1; i <= 4; i++) {
        var seg = document.getElementById('ps-' + i);
        seg.className = 'h-1.5 flex-1 rounded-full transition-colors duration-300 ';
        seg.className += (i <= score) ? colors[score - 1] : 'bg-border-dark';
    }
    document.getElementById('pwd-strength-label').textContent = score > 0 ? labels[score - 1] : '';
    document.getElementById('pwd-strength-label').style.color = ['#EF4444','#F97316','#F59E0B','#22C55E'][score - 1] || '';
}

document.getElementById('f-password').addEventListener('input', function() {
    updateStrengthBar(this.value);
});

// ─── Toggle password show/hide (form) ────────────────────────────
function toggleFieldPassword(inputId, eyeId) {
    var input = document.getElementById(inputId);
    var eye   = document.getElementById(eyeId);
    if (input.type === 'password') {
        input.type  = 'text';
        eye.className = eye.className.replace('bx-show', 'bx-hide');
    } else {
        input.type  = 'password';
        eye.className = eye.className.replace('bx-hide', 'bx-show');
    }
}

// ─── Copy helpers ─────────────────────────────────────────────────
function copyText(text, label) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
        showToast('success', (label || 'Text') + ' disalin!');
    }).catch(function() {
        legacyCopy(text, label);
    });
}

function copyFromSpan(spanId, label) {
    var el   = document.getElementById(spanId);
    var text = el ? el.textContent.trim() : '';
    if (!text || text === '-') return;
    copyText(text, label);
}

function copyPassword(id, passwordJson) {
    // passwordJson is already a JSON-encoded string (may include quotes)
    var pwd = passwordJson;
    // strip surrounding quotes if it came as a json string literal
    try {
        var parsed = JSON.parse(passwordJson);
        if (typeof parsed === 'string') pwd = parsed;
    } catch(e) {}
    if (!pwd || pwd === 'null' || pwd === '') {
        showToast('error', 'Password kosong');
        return;
    }
    navigator.clipboard.writeText(pwd).then(function() {
        showToast('success', 'Password disalin!');
    }).catch(function() {
        legacyCopy(pwd, 'Password');
    });
}

function legacyCopy(text, label) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity  = '0';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        showToast('success', (label || 'Text') + ' disalin!');
    } catch(e) {
        showToast('error', 'Gagal menyalin');
    }
    document.body.removeChild(ta);
}

// ─── Delete confirm ───────────────────────────────────────────────
function deleteCredential(formId, label) {
    SwalDanger.fire({
        title: 'Hapus Credential?',
        html: 'Credential <strong>"' + label + '"</strong> akan dihapus permanen.',
        icon: 'warning',
        confirmButtonText: '<i class="bx bx-trash"></i> Ya, hapus!',
    }).then(function(result) {
        if (result.isConfirmed) document.getElementById(formId).submit();
    });
}

// ─── Toast helper ─────────────────────────────────────────────────
function showToast(icon, title, text) {
    SwalToast.fire({ icon: icon, title: title, text: text || '' });
}

// ─── Utility ──────────────────────────────────────────────────────
function ucfirstType(str) {
    if (!str) return '-';
    return str.replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeViewModal();
        closeFormModal();
    }
});
</script>

@endsection
