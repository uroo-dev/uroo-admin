@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')
<div x-data="{
    editModalOpen: false,
    editMode: false,
    editingId: null,
    formAction: '{{ route('clients.store') }}',
    formData: { name:'', email:'', phone:'', whatsapp:'', company:'', address:'', website:'', status:'active', notes:'' },
    editClient(el) {
        this.editMode = true;
        this.editingId = el.dataset.id;
        this.formAction = '{{ route('clients.update', [':id']) }}'.replace(':id', el.dataset.id);
        this.formData = {
            name: el.dataset.name,
            email: el.dataset.email,
            phone: el.dataset.phone,
            whatsapp: el.dataset.whatsapp,
            company: el.dataset.company,
            address: el.dataset.address,
            website: el.dataset.website,
            status: el.dataset.status,
            notes: el.dataset.notes
        };
        this.editModalOpen = true;
    },
    deleteClient(formId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Batal',
            background: '#FFFFFF',
            customClass: {
                popup: 'border-4 border-border-dark rounded-modal shadow-hard'
            }
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
        this.formData = { name:'', email:'', phone:'', whatsapp:'', company:'', address:'', website:'', status:'active', notes:'' };
        this.editModalOpen = true;
    }
}">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-group text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-user-check text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['active'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Active</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-briefcase text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['inactive'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Inactive</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-acc rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-time-five text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ ($stats['active'] ?? 0) + ($stats['inactive'] ?? 0) }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Recent</p>
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
                        <th class="text-left px-6 py-4 font-extrabold">Phone</th>
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
                            <td class="px-6 py-4 text-txt-secondary">{{ $client->phone }}</td>
                            <td class="px-6 py-4 text-txt-secondary">{{ $client->company }}</td>
                            <td class="px-6 py-4">
                                @if ($client->status === 'active')
                                    <x-badge variant="success">Active</x-badge>
                                @else
                                    <x-badge variant="danger">Inactive</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        @click="editClient($el)"
                                        class="p-2 text-txt-secondary hover:text-primary transition-colors"
                                        data-id="{{ $client->id }}"
                                        data-name="{{ e($client->name) }}"
                                        data-email="{{ e($client->email ?? '') }}"
                                        data-phone="{{ e($client->phone ?? '') }}"
                                        data-whatsapp="{{ e($client->whatsapp ?? '') }}"
                                        data-company="{{ e($client->company ?? '') }}"
                                        data-address="{{ e($client->address ?? '') }}"
                                        data-website="{{ e($client->website ?? '') }}"
                                        data-status="{{ $client->status }}"
                                        data-notes="{{ e($client->notes ?? '') }}"
                                    >
                                        <i class="bx bx-edit text-base"></i>
                                    </button>
                                    <form id="delete-form-{{ $client->id }}" action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="deleteClient('delete-form-{{ $client->id }}')" class="p-2 text-txt-secondary hover:text-danger transition-colors">
                                            <i class="bx bx-trash text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="bx bx-user text-5xl text-txt-secondary"></i>
                                <p class="text-txt-secondary font-medium mt-3">No clients found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $clients->links() }}
    </div>

    {{-- Modal Form --}}
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
         @click.outside="editModalOpen = false">
        <div x-show="editModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0"
             @click.stop
             class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg animate-scale-in">
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold" x-text="editMode ? 'Edit Client' : 'Add Client'"></h3>
                <button @click="editModalOpen = false" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form method="POST" :action="formAction" class="p-6 space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="bg-danger/10 border-4 border-danger rounded-card p-4 mb-4">
                        <ul class="text-sm text-danger font-medium space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="modal-name" class="block text-sm font-semibold text-txt-primary">Name</label>
                        <input type="text" id="modal-name" name="name" x-model="formData.name"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="Client name">
                    </div>
                    <div class="space-y-1.5">
                        <label for="modal-email" class="block text-sm font-semibold text-txt-primary">Email</label>
                        <input type="email" id="modal-email" name="email" x-model="formData.email"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="client@email.com">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="modal-phone" class="block text-sm font-semibold text-txt-primary">Phone</label>
                        <input type="text" id="modal-phone" name="phone" x-model="formData.phone"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="08123456789">
                    </div>
                    <div class="space-y-1.5">
                        <label for="modal-whatsapp" class="block text-sm font-semibold text-txt-primary">WhatsApp</label>
                        <input type="text" id="modal-whatsapp" name="whatsapp" x-model="formData.whatsapp"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="628123456789">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label for="modal-company" class="block text-sm font-semibold text-txt-primary">Company</label>
                    <input type="text" id="modal-company" name="company" x-model="formData.company"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                        placeholder="PT. Example">
                </div>
                <div class="space-y-1.5">
                    <label for="modal-address" class="block text-sm font-semibold text-txt-primary">Address</label>
                    <input type="text" id="modal-address" name="address" x-model="formData.address"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                        placeholder="Jl. Merdeka No. 1">
                </div>
                <div class="space-y-1.5">
                    <label for="modal-website" class="block text-sm font-semibold text-txt-primary">Website</label>
                    <input type="url" id="modal-website" name="website" x-model="formData.website"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                        placeholder="https://example.com">
                </div>
                <div class="space-y-1.5">
                    <label for="modal-status" class="block text-sm font-semibold text-txt-primary">Status</label>
                    <select id="modal-status" name="status" x-model="formData.status"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="modal-notes" class="block text-sm font-semibold text-txt-primary">Notes</label>
                    <textarea id="modal-notes" name="notes" rows="3" x-model="formData.notes"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                        placeholder="Notes..."></textarea>
                </div>
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