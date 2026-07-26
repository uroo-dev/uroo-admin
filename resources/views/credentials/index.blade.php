@extends('layouts.app')

@section('title', 'Credential Vault')
@section('page-title', 'Credential Vault')

@section('content')
@php
    $typeIcons = [
        'api_key'  => ['icon' => 'bx-key',      'color' => 'bg-yellow-acc'],
        'ssh'      => ['icon' => 'bx-terminal',  'color' => 'bg-[#111827]'],
        'database' => ['icon' => 'bx-data',      'color' => 'bg-secondary'],
        'email'    => ['icon' => 'bx-envelope',  'color' => 'bg-pink-acc'],
        'ftp'      => ['icon' => 'bx-upload',    'color' => 'bg-cyan-acc'],
        'cloud'    => ['icon' => 'bx-cloud',     'color' => 'bg-[#22C55E]'],
        'cpanel'   => ['icon' => 'bx-crown',     'color' => 'bg-purple-acc'],
        'hosting'  => ['icon' => 'bx-server',    'color' => 'bg-primary'],
        'vps'      => ['icon' => 'bx-desktop',   'color' => 'bg-[#F59E0B]'],
    ];
@endphp

{{-- ─── Stats ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                <i class="bx bx-lock-alt text-white text-[28px]"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold">{{ $stats['total'] }}</p>
                <p class="text-sm font-medium text-txt-secondary">Total</p>
            </div>
        </div>
    </div>
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
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
            <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
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
            <div class="w-14 h-14 bg-purple-acc rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                <i class="bx bx-category text-white text-[28px]"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold">{{ count($stats['byType']) }}</p>
                <p class="text-sm font-medium text-txt-secondary">Categories</p>
            </div>
        </div>
    </div>
</div>

{{-- ─── Toolbar ─────────────────────────────────────────────────────────── --}}
<div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
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
                    <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium select-none">
                <input type="checkbox" name="favorites" value="1" {{ $showFavorites ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 accent-primary">
                <i class="bx bx-star text-[#F59E0B]"></i>
                <span>Favorites</span>
            </label>
            <button type="submit" class="px-4 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <i class="bx bx-search-alt"></i> Search
            </button>
        </div>
        <button type="button" onclick="openAddModal()"
            class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 self-start sm:self-auto">
            <i class="bx bx-plus text-lg"></i> Add Credential
        </button>
    </form>
</div>

{{-- ─── Cards Grid ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse ($credentials as $credential)
        @php
            $ti = $typeIcons[$credential->type] ?? ['icon' => 'bx-lock-alt', 'color' => 'bg-primary'];
            $iconText = in_array($credential->type, ['ssh', 'api_key', 'ftp', 'cyan-acc']) ? 'text-white' : 'text-[#111827]';
            $iconText = in_array($credential->type, ['hosting', 'database', 'cpanel', 'cloud', 'ssh']) ? 'text-white' : 'text-[#111827]';
        @endphp
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex flex-col gap-4 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">

            {{-- Card Header --}}
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 {{ $ti['color'] }} rounded-button flex items-center justify-center shadow-hard-sm flex-shrink-0">
                    <i class="bx {{ $ti['icon'] }} text-white text-[22px]"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-extrabold text-sm leading-tight truncate">{{ $credential->label }}</h3>
                    <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full bg-primary/10 text-primary mt-0.5">
                        {{ ucfirst(str_replace('_', ' ', $credential->type)) }}
                    </span>
                </div>
                {{-- Favorite star --}}
                <form method="POST" action="{{ route('credentials.update', $credential) }}" class="flex-shrink-0">
                    @csrf @method('PUT')
                    <input type="hidden" name="is_favorite" value="{{ $credential->is_favorite ? '0' : '1' }}">
                    {{-- pass required fields through --}}
                    <input type="hidden" name="label" value="{{ $credential->label }}">
                    <input type="hidden" name="type" value="{{ $credential->type }}">
                    <button type="submit" title="{{ $credential->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}"
                        class="p-1.5 rounded-lg hover:scale-110 active:scale-95 transition-all duration-200">
                        <i class="bx {{ $credential->is_favorite ? 'bxs-star text-[#F59E0B]' : 'bx-star text-txt-secondary' }} text-xl"></i>
                    </button>
                </form>
            </div>

            {{-- Key Info --}}
            <div class="space-y-1.5 text-sm">
                @if ($credential->provider)
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-building text-base"></i> Provider</span>
                        <span class="font-semibold truncate max-w-[60%] text-right">{{ $credential->provider }}</span>
                    </div>
                @endif
                @if ($credential->domain)
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-globe text-base"></i> Domain</span>
                        <span class="font-semibold truncate max-w-[60%] text-right">{{ $credential->domain }}</span>
                    </div>
                @endif
                @if ($credential->username)
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-user text-base"></i> Username</span>
                        <span class="font-semibold truncate max-w-[60%] text-right font-mono text-xs bg-bgmain px-2 py-0.5 rounded">{{ $credential->username }}</span>
                    </div>
                @endif
                @if ($credential->host_ip)
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-server text-base"></i> Host / IP</span>
                        <span class="font-semibold font-mono text-xs bg-bgmain px-2 py-0.5 rounded">{{ $credential->host_ip }}</span>
                    </div>
                @endif
                @if ($credential->expires_at)
                    @php $expired = $credential->expires_at->isPast(); @endphp
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-calendar text-base"></i> Expires</span>
                        <span class="font-semibold text-xs px-2 py-0.5 rounded {{ $expired ? 'bg-red-100 text-danger' : 'bg-green-100 text-[#22C55E]' }}">
                            {{ $credential->expires_at->format('d M Y') }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 pt-3 border-t-4 border-border-dark mt-auto">
                <button type="button"
                    onclick="openDetailModal({{ $credential->id }}, {{ json_encode($credential->label) }}, {{ json_encode($credential->type) }}, {{ json_encode($credential->provider) }}, {{ json_encode($credential->domain) }}, {{ json_encode($credential->host_ip) }}, {{ json_encode($credential->username) }}, {{ json_encode($credential->password) }}, {{ json_encode($credential->database_name) }}, {{ json_encode($credential->database_user) }}, {{ json_encode($credential->database_password) }}, {{ json_encode($credential->ssh_key) }}, {{ json_encode($credential->auth_url) }}, {{ json_encode($credential->notes) }})"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-primary text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out">
                    <i class="bx bx-show text-sm"></i> View Details
                </button>
                <button type="button"
                    onclick="openEditModal({{ $credential->id }}, {{ json_encode($credential->label) }}, {{ json_encode($credential->type) }})"
                    class="px-3 py-2 font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out"
                    title="Edit">
                    <i class="bx bx-edit text-base"></i>
                </button>
                <form id="del-cred-{{ $credential->id }}" method="POST" action="{{ route('credentials.destroy', $credential) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="button"
                        onclick="deleteCredential('del-cred-{{ $credential->id }}', {{ json_encode($credential->label) }})"
                        class="px-3 py-2 text-danger font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 hover:bg-danger/10 transition-all duration-200 ease-out"
                        title="Delete">
                        <i class="bx bx-trash text-base"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <div class="w-20 h-20 bg-primary/10 rounded-button flex items-center justify-center mb-4">
                <i class="bx bx-lock-alt text-5xl text-primary"></i>
            </div>
            <h3 class="text-xl font-extrabold">No credentials yet</h3>
            <p class="text-txt-secondary mt-2 mb-6">Securely store your passwords, API keys, and more</p>
            <button type="button" onclick="openAddModal()"
                class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <i class="bx bx-plus"></i> Add First Credential
            </button>
        </div>
    @endforelse
</div>

@if ($credentials->hasPages())
    <div class="mt-8">{{ $credentials->links() }}</div>
@endif


{{-- ─── Detail Modal ────────────────────────────────────────────────────── --}}
<div id="detail-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
    onclick="if(event.target===this)closeDetailModal()">
    <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg max-h-[90vh] overflow-y-auto animate-scale-in"
        onclick="event.stopPropagation()">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark sticky top-0 bg-surface z-10">
            <div class="flex items-center gap-3">
                <div id="dm-icon-wrap" class="w-10 h-10 rounded-button flex items-center justify-center shadow-hard-sm">
                    <i id="dm-icon" class="bx text-white text-xl"></i>
                </div>
                <div>
                    <h3 id="dm-label" class="text-lg font-extrabold leading-tight"></h3>
                    <span id="dm-type-badge" class="text-xs font-bold px-2 py-0.5 rounded-full bg-primary/10 text-primary"></span>
                </div>
            </div>
            <button onclick="closeDetailModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                <i class="bx bx-x"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 space-y-3">

            {{-- Label --}}
            <div class="flex items-center justify-between py-2.5 px-3 rounded-input bg-bgmain border-2 border-border-dark">
                <span class="flex items-center gap-2 text-sm text-txt-secondary font-medium"><i class="bx bx-tag"></i> Label</span>
                <span id="dm-label-val" class="text-sm font-bold"></span>
            </div>

            {{-- Username --}}
            <div id="dm-row-username" class="hidden flex items-center justify-between py-2.5 px-3 rounded-input bg-bgmain border-2 border-border-dark">
                <span class="flex items-center gap-2 text-sm text-txt-secondary font-medium"><i class="bx bx-user"></i> Username</span>
                <div class="flex items-center gap-2">
                    <span id="dm-username" class="text-sm font-bold font-mono"></span>
                    <button onclick="copyText('dm-username', this)" class="p-1 rounded hover:bg-primary/10 hover:text-primary transition-colors" title="Copy">
                        <i class="bx bx-copy text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Password --}}
            <div id="dm-row-password" class="hidden py-3 px-3 rounded-input bg-bgmain border-2 border-border-dark">
                <div class="flex items-center justify-between mb-2">
                    <span class="flex items-center gap-2 text-sm text-txt-secondary font-medium"><i class="bx bx-lock-alt"></i> Password</span>
                    <div class="flex items-center gap-1">
                        <button onclick="togglePassword('dm-password')" class="p-1.5 rounded hover:bg-primary/10 hover:text-primary transition-colors" title="Tampilkan/sembunyikan">
                            <i id="dm-password-eye" class="bx bx-show text-base"></i>
                        </button>
                        <button onclick="copyText('dm-password', this)" class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-button border-2 border-border-dark bg-surface hover:-translate-y-0.5 transition-all duration-200 ease-out" title="Copy password">
                            <i class="bx bx-copy text-sm"></i> Copy
                        </button>
                    </div>
                </div>
                <span id="dm-password" data-value="" class="block text-base font-bold font-mono tracking-widest select-none">••••••••••••</span>
            </div>

            {{-- No password notice --}}
            <div id="dm-row-no-password" class="hidden py-2.5 px-3 rounded-input bg-bgmain border-2 border-border-dark">
                <p class="text-sm text-txt-secondary text-center">Tidak ada password tersimpan</p>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 border-t-4 border-border-dark flex justify-end gap-3">
            <button onclick="closeDetailModal()"
                class="px-6 py-2.5 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                Close
            </button>
        </div>
    </div>
</div>


{{-- ─── Add / Edit Modal ────────────────────────────────────────────────── --}}
<div id="form-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
    onclick="if(event.target===this)closeFormModal()">
    <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-xl max-h-[90vh] overflow-y-auto animate-scale-in"
        onclick="event.stopPropagation()">

        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark sticky top-0 bg-surface z-10">
            <h3 id="form-modal-title" class="text-lg font-extrabold">Add Credential</h3>
            <button onclick="closeFormModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                <i class="bx bx-x"></i>
            </button>
        </div>

        <form id="credential-form" method="POST" action="{{ route('credentials.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">

            <div>
                <label class="block text-sm font-semibold text-txt-primary mb-1.5">Label <span class="text-red-500">*</span></label>
                <input type="text" name="label" id="f-label" required placeholder="e.g. Production Server, GitHub Token"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-semibold text-txt-primary mb-1.5">Type <span class="text-red-500">*</span></label>
                <input type="text" name="type" id="f-type" required placeholder="e.g. hosting, ssh, database, api_key"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                <p class="text-xs text-txt-secondary mt-1">Contoh: hosting, vps, ssh, database, cpanel, cloud, ftp, api_key, email</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-txt-primary mb-1.5">Username / Email</label>
                <input type="text" name="username" id="f-username" placeholder="username atau email"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-semibold text-txt-primary mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="f-password" placeholder="••••••••"
                        class="w-full px-4 py-3 pr-11 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    <button type="button" onclick="toggleFieldPassword('f-password', 'f-pwd-eye')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-txt-secondary hover:text-primary transition-colors">
                        <i id="f-pwd-eye" class="bx bx-show text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeFormModal()"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Save Credential
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─── JavaScript ──────────────────────────────────────────────────────── --}}
<script>
const typeColors = {
    api_key:'bg-yellow-acc', ssh:'bg-[#111827]', database:'bg-secondary',
    email:'bg-pink-acc', ftp:'bg-cyan-acc', cloud:'bg-[#22C55E]',
    cpanel:'bg-purple-acc', hosting:'bg-primary', vps:'bg-[#F59E0B]'
};
const typeIcons = {
    api_key:'bx-key', ssh:'bx-terminal', database:'bx-data',
    email:'bx-envelope', ftp:'bx-upload', cloud:'bx-cloud',
    cpanel:'bx-crown', hosting:'bx-server', vps:'bx-desktop'
};

// ── Detail Modal ──────────────────────────────────────────────────────────
function openDetailModal(id, label, type, provider, domain, hostIp, username, password, dbName, dbUser, dbPassword, sshKey, authUrl, notes) {
    // Label
    document.getElementById('dm-label').textContent     = label;
    document.getElementById('dm-label-val').textContent = label;
    document.getElementById('dm-type-badge').textContent = type.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
    const wrap = document.getElementById('dm-icon-wrap');
    wrap.className = 'w-10 h-10 rounded-button flex items-center justify-center shadow-hard-sm ' + (typeColors[type] || 'bg-primary');
    document.getElementById('dm-icon').className = 'bx ' + (typeIcons[type] || 'bx-lock-alt') + ' text-white text-xl';

    // Username
    const uRow = document.getElementById('dm-row-username');
    if (username) {
        document.getElementById('dm-username').textContent = username;
        uRow.classList.remove('hidden');
    } else {
        uRow.classList.add('hidden');
    }

    // Password
    const pwdRow  = document.getElementById('dm-row-password');
    const noPwdRow = document.getElementById('dm-row-no-password');
    const pwdEl   = document.getElementById('dm-password');
    if (password) {
        pwdEl.dataset.value = password;
        pwdEl.textContent   = '••••••••••••';
        document.getElementById('dm-password-eye').className = 'bx bx-show text-base';
        pwdRow.classList.remove('hidden');
        noPwdRow.classList.add('hidden');
    } else {
        pwdRow.classList.add('hidden');
        noPwdRow.classList.remove('hidden');
    }

    document.getElementById('detail-modal').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

function togglePassword(elId) {
    const el = document.getElementById(elId);
    const eye = document.getElementById(elId + '-eye');
    if (el.textContent.includes('•')) {
        el.textContent = el.dataset.value;
        eye.className = eye.className.replace('bx-show', 'bx-hide');
    } else {
        el.textContent = '••••••••••••';
        eye.className = eye.className.replace('bx-hide', 'bx-show');
    }
}

function copyText(elId, btn) {
    const el = document.getElementById(elId);
    const text = el.dataset.value || el.textContent;
    navigator.clipboard.writeText(text.trim()).then(() => {
        const icon = btn.querySelector('i');
        const prev = icon.className;
        icon.className = icon.className.replace('bx-copy', 'bx-check');
        btn.classList.add('text-[#22C55E]');
        setTimeout(() => {
            icon.className = prev;
            btn.classList.remove('text-[#22C55E]');
        }, 1500);
    });
}

// ── Add / Edit Form Modal ─────────────────────────────────────────────────
function openAddModal() {
    document.getElementById('form-modal-title').textContent = 'Add Credential';
    document.getElementById('credential-form').action = '{{ route('credentials.store') }}';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('credential-form').reset();
    document.getElementById('form-modal').classList.remove('hidden');
}

function openEditModal(id, label, type) {
    document.getElementById('form-modal-title').textContent = 'Edit Credential';
    document.getElementById('credential-form').action = '{{ url('credentials') }}/' + id;
    document.getElementById('form-method').value = 'PUT';

    document.getElementById('f-label').value    = label || '';
    document.getElementById('f-type').value     = type  || '';
    document.getElementById('f-username').value = '';
    document.getElementById('f-password').value = '';

    document.getElementById('form-modal').classList.remove('hidden');
}

function closeFormModal() {
    document.getElementById('form-modal').classList.add('hidden');
}

function toggleFieldPassword(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(eyeId);
    if (input.type === 'password') {
        input.type = 'text';
        eye.className = eye.className.replace('bx-show', 'bx-hide');
    } else {
        input.type = 'password';
        eye.className = eye.className.replace('bx-hide', 'bx-show');
    }
}

function deleteCredential(formId, label) {
    Swal.fire({
        title: 'Hapus Credential?',
        text: '"' + label + '" akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        background: '#FFFFFF',
        customClass: { popup: 'border-4 border-border-dark rounded-modal shadow-hard' }
    }).then((result) => {
        if (result.isConfirmed) document.getElementById(formId).submit();
    });
}
</script>

@endsection
