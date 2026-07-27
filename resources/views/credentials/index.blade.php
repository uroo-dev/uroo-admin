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

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-lock-alt text-white text-[28px]"></i></div>
            <div><p class="text-3xl font-extrabold">{{ $stats['total'] }}</p><p class="text-sm font-medium text-txt-secondary">Total</p></div>
        </div>
    </div>
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-star text-white text-[28px]"></i></div>
            <div><p class="text-3xl font-extrabold">{{ $stats['favorites'] }}</p><p class="text-sm font-medium text-txt-secondary">Favorites</p></div>
        </div>
    </div>
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-time text-white text-[28px]"></i></div>
            <div><p class="text-3xl font-extrabold">{{ $stats['expiring'] }}</p><p class="text-sm font-medium text-txt-secondary">Expiring Soon</p></div>
        </div>
    </div>
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-purple-acc rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-category text-white text-[28px]"></i></div>
            <div><p class="text-3xl font-extrabold">{{ count($stats['byType']) }}</p><p class="text-sm font-medium text-txt-secondary">Categories</p></div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
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
                <i class="bx bx-star text-[#F59E0B]"></i> Favorites
            </label>
        </div>
        <button type="button" onclick="openAddModal()"
            class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 self-start sm:self-auto">
            <i class="bx bx-plus text-lg"></i> Add Credential
        </button>
    </form>
</div>

{{-- Cards Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse ($credentials as $credential)
        @php
            $ti = $typeIcons[$credential->type] ?? ['icon' => 'bx-lock-alt', 'color' => 'bg-primary'];
        @endphp
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex flex-col gap-4 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
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
                <form method="POST" action="{{ route('credentials.update', $credential) }}" class="flex-shrink-0">
                    @csrf @method('PUT')
                    <input type="hidden" name="label" value="{{ $credential->label }}">
                    <input type="hidden" name="type" value="{{ $credential->type }}">
                    <input type="hidden" name="is_favorite" value="{{ $credential->is_favorite ? '0' : '1' }}">
                    <button type="submit" title="{{ $credential->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}"
                        class="p-1.5 rounded-lg hover:scale-110 active:scale-95 transition-all duration-200">
                        <i class="bx {{ $credential->is_favorite ? 'bxs-star text-[#F59E0B]' : 'bx-star text-txt-secondary' }} text-xl"></i>
                    </button>
                </form>
            </div>

            {{-- Key Info --}}
            <div class="space-y-1.5 text-sm">
                @if ($credential->username)
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-user text-base"></i> Username</span>
                        <span class="font-semibold truncate max-w-[60%] text-right font-mono text-xs bg-bgmain px-2 py-0.5 rounded">{{ $credential->username }}</span>
                    </div>
                @endif
                @if ($credential->password)
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-lock-alt text-base"></i> Password</span>
                        <span class="font-semibold font-mono text-xs bg-bgmain px-2 py-0.5 rounded">••••••••</span>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 pt-3 border-t-4 border-border-dark mt-auto">
                <form id="del-cred-{{ $credential->id }}" method="POST" action="{{ route('credentials.destroy', $credential) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" onclick="deleteCredential('del-cred-{{ $credential->id }}', {{ json_encode($credential->label) }})"
                        class="px-3 py-2 text-danger font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 hover:bg-danger/10 transition-all duration-200 ease-out" title="Delete">
                        <i class="bx bx-trash text-base"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <div class="w-20 h-20 bg-primary/10 rounded-button flex items-center justify-center mb-4"><i class="bx bx-lock-alt text-5xl text-primary"></i></div>
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

{{-- Add / Edit Modal --}}
<div id="form-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeFormModal()">
    <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-xl max-h-[90vh] overflow-y-auto animate-scale-in" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark sticky top-0 bg-surface z-10">
            <h3 id="form-modal-title" class="text-lg font-extrabold">Add Credential</h3>
            <button onclick="closeFormModal()" class="text-2xl text-txt-secondary hover:text-danger">&times;</button>
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
                <select name="type" id="f-type" required
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    <option value="">Select type</option>
                    @foreach (App\Models\Credential::types() as $t)
                        <option value="{{ $t }}">{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-txt-secondary mt-1">e.g. hosting, vps, ssh, database, cpanel, cloud, ftp, api_key, email</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-txt-primary mb-1.5">Username / Email</label>
                <input type="text" name="username" id="f-username" placeholder="username atau email"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-semibold text-txt-primary mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="f-password" placeholder="Leave empty for auto-generated"
                        class="w-full px-4 py-3 pr-11 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    <button type="button" onclick="toggleFieldPassword('f-password', 'f-pwd-eye')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-txt-secondary hover:text-primary transition-colors">
                        <i id="f-pwd-eye" class="bx bx-show text-lg"></i>
                    </button>
                </div>
                <p class="text-xs text-txt-secondary mt-1">If left empty, a strong password will be auto-generated</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeFormModal()"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Cancel</button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Save Credential</button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript --}}
<script>
function openAddModal() {
    document.getElementById('form-modal-title').textContent = 'Add Credential';
    document.getElementById('credential-form').action = '{{ route('credentials.store') }}';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('credential-form').reset();
    document.getElementById('form-modal').classList.remove('hidden');
}

function openEditModal(id, label, type, username) {
    document.getElementById('form-modal-title').textContent = 'Edit Credential';
    document.getElementById('credential-form').action = '{{ url('credentials') }}/' + id;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('f-label').value = label || '';
    document.getElementById('f-type').value = type || '';
    document.getElementById('f-username').value = username || '';
    document.getElementById('f-password').value = '';
    document.getElementById('form-modal').classList.remove('hidden');
}

function closeFormModal() {
    document.getElementById('form-modal').classList.add('hidden');
}

function toggleFieldPassword(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
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