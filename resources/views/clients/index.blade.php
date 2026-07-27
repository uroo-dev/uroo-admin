@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')
<div x-data="{
    editModalOpen: false,
    editMode: false,
    editingId: null,
    formAction: '{{ route('clients.store') }}',
    formData: { name:'', email:'', whatsapp:'+62', company:'', address:'', status:'pending', notes:'' },
    editClient(el) {
        this.editMode = true;
        this.editingId = el.dataset.id;
        this.formAction = '{{ route('clients.update', [':id']) }}'.replace(':id', el.dataset.id);
        this.formData = {
            name:      el.dataset.name,
            email:     el.dataset.email,
            whatsapp:  el.dataset.whatsapp || '+62',
            company:   el.dataset.company,
            address:   el.dataset.address,
            status:    el.dataset.status,
            notes:     el.dataset.notes
        };
        this.editModalOpen = true;
    },
    deleteClient(formId) {
        SwalDanger.fire({
            title: 'Hapus Client?',
            text: 'Data yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus!',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    },
    resetForm() {
        this.editMode = false;
        this.editingId = null;
        this.formAction = '{{ route('clients.store') }}';
        this.formData = { name:'', email:'', whatsapp:'+62', company:'', address:'', status:'pending', notes:'' };
        this.editModalOpen = true;
    },
    openWhatsApp(number) {
        if (!number || number === '+62' || number.trim() === '') {
            SwalToast.fire({ icon: 'warning', title: 'Nomor WhatsApp belum diisi.' });
            return;
        }
        /* Normalisasi: hapus semua non-digit, pastikan awalan 62 */
        let clean = number.replace(/\D/g, '');
        if (clean.startsWith('0')) clean = '62' + clean.slice(1);
        if (!clean.startsWith('62')) clean = '62' + clean;
        window.open('https://wa.me/' + clean, '_blank');
    }
}">

    {{-- Flash Success --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                SwalToast.fire({ icon: 'success', title: '{{ session('success') }}' });
            });
        </script>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        {{-- Total --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bxs-user-detail text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Clients</p>
                </div>
            </div>
        </div>

        {{-- Deal --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bxs-badge-check text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['deal'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Deal</p>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bxs-hourglass text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['pending'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Pending</p>
                </div>
            </div>
        </div>

        {{-- Canceled --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#EF4444] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bxs-x-circle text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['canceled'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Canceled</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <form method="GET" action="{{ route('clients.index') }}" class="relative w-full sm:w-80">
                <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Search clients..."
                    class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </form>
            <button @click="resetForm()"
                class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 w-full sm:w-auto">
                <i class="bx bx-plus text-lg"></i>
                Add Client
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-4 border-border-dark bg-gray-50">
                        <th class="text-left px-6 py-4 font-extrabold">Name</th>
                        <th class="text-left px-6 py-4 font-extrabold">Email</th>
                        <th class="text-left px-6 py-4 font-extrabold">Company</th>
                        <th class="text-left px-6 py-4 font-extrabold">Status</th>
                        <th class="text-right px-6 py-4 font-extrabold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr class="border-b-2 border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-semibold">{{ $client->name }}</td>
                            <td class="px-6 py-4 text-txt-secondary">{{ $client->email }}</td>
                            <td class="px-6 py-4 text-txt-secondary">{{ $client->company }}</td>
                            <td class="px-6 py-4">
                                @if ($client->status === 'deal')
                                    <x-badge variant="warning">Deal</x-badge>
                                @elseif($client->status === 'pending')
                                    <x-badge variant="info">Pending</x-badge>
                                @else
                                    <x-badge variant="danger">Canceled</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- WhatsApp: hijau --}}
                                    <button
                                        @click="openWhatsApp('{{ e($client->whatsapp ?? '') }}')"
                                        title="Chat WhatsApp"
                                        class="w-9 h-9 flex items-center justify-center bg-[#25D366] text-white rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out">
                                        <i class="bx bxl-whatsapp text-lg"></i>
                                    </button>

                                    {{-- Edit: biru --}}
                                    <button
                                        @click="editClient($el)"
                                        title="Edit Client"
                                        class="w-9 h-9 flex items-center justify-center bg-secondary text-white rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out"
                                        data-id="{{ $client->id }}"
                                        data-name="{{ e($client->name) }}"
                                        data-email="{{ e($client->email ?? '') }}"
                                        data-whatsapp="{{ e($client->whatsapp ?? '') }}"
                                        data-company="{{ e($client->company ?? '') }}"
                                        data-address="{{ e($client->address ?? '') }}"
                                        data-status="{{ $client->status }}"
                                        data-notes="{{ e($client->notes ?? '') }}"
                                    >
                                        <i class="bx bxs-edit text-base"></i>
                                    </button>

                                    {{-- Delete: merah --}}
                                    <form id="delete-form-{{ $client->id }}"
                                          action="{{ route('clients.destroy', $client) }}"
                                          method="POST"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            @click="deleteClient('delete-form-{{ $client->id }}')"
                                            title="Delete Client"
                                            class="w-9 h-9 flex items-center justify-center bg-[#EF4444] text-white rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out">
                                            <i class="bx bxs-trash text-base"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <i class="bx bxs-user-x text-6xl text-txt-secondary"></i>
                                <p class="text-txt-secondary font-semibold mt-3">No clients found</p>
                                <p class="text-xs text-txt-secondary mt-1">Try a different search or add a new client.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($clients->hasPages())
        <div class="mt-6">
            {{ $clients->appends(['q' => $search])->links('vendor.pagination.neo-brutalism') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         Modal Create / Edit Client
    ══════════════════════════════════════════════════ --}}
    <div x-show="editModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         @keydown.escape.window="editModalOpen = false"
         @click.self="editModalOpen = false">

        <div x-show="editModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0"
             @click.stop
             class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg max-h-[85vh] overflow-y-auto">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold" x-text="editMode ? 'Edit Client' : 'Add Client'"></h3>
                <button @click="editModalOpen = false"
                    class="w-9 h-9 flex items-center justify-center text-txt-secondary hover:text-[#EF4444] hover:bg-red-50 rounded-button transition-colors">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" :action="formAction" class="p-6 space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="bg-[#EF4444]/10 border-4 border-[#EF4444] rounded-card p-4">
                        <ul class="text-sm text-[#EF4444] font-medium space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Name + Email --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-txt-primary">Name</label>
                        <input type="text" name="name" x-model="formData.name"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="Client name">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-txt-primary">Email</label>
                        <input type="email" name="email" x-model="formData.email"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="client@email.com">
                    </div>
                </div>

                {{-- WhatsApp — dengan prefix +62 otomatis dan icon WA --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">
                        WhatsApp
                        <span class="text-xs font-normal text-txt-secondary ml-1">(awalan +62 otomatis)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-1 text-[#25D366] font-bold text-sm pointer-events-none select-none">
                            <i class="bx bxl-whatsapp text-base"></i>
                        </span>
                        <input
                            type="tel"
                            name="whatsapp"
                            x-model="formData.whatsapp"
                            @focus="if (!formData.whatsapp || formData.whatsapp === '') formData.whatsapp = '+62'"
                            @input="
                                let v = formData.whatsapp;
                                if (!v.startsWith('+62')) {
                                    let digits = v.replace(/\D/g, '');
                                    if (digits.startsWith('62')) digits = digits.slice(2);
                                    else if (digits.startsWith('0')) digits = digits.slice(1);
                                    formData.whatsapp = '+62' + digits;
                                }
                            "
                            class="w-full pl-9 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-[#25D366] outline-none transition-colors"
                            placeholder="+628123456789">
                    </div>
                </div>

                {{-- Company --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Company</label>
                    <input type="text" name="company" x-model="formData.company"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                        placeholder="PT. Example">
                </div>

                {{-- Address --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Address</label>
                    <input type="text" name="address" x-model="formData.address"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                        placeholder="Jl. Merdeka No. 1">
                </div>

                {{-- Status --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Status</label>
                    <select name="status" x-model="formData.status"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                        <option value="deal">Deal</option>
                        <option value="pending">Pending</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>

                {{-- Notes --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Notes</label>
                    <textarea name="notes" rows="3" x-model="formData.notes"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                        placeholder="Notes..."></textarea>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="editModalOpen = false"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        <span x-text="editMode ? 'Update Client' : 'Save Client'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
