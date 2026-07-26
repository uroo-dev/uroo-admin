@extends('layouts.app')
@section('title', 'Quality Control')
@section('page-title', 'Quality Control')
@section('content')

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded',()=>Swal.fire({icon:'success',title:'Berhasil!',text:'{{ session('success') }}',timer:3000,showConfirmButton:false,toast:true,position:'top-end',background:'#FFFFFF',customClass:{popup:'border-4 border-border-dark rounded-card shadow-hard'}}));
</script>
@endif

{{-- Toolbar --}}
<div class="flex items-center justify-between gap-4 mb-8">
    <p class="text-txt-secondary text-sm">Manage your deployment checklists and quality assurance</p>
    <button type="button" onclick="openQcModal()"
        class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
        <i class="bx bx-plus text-lg"></i> New Checklist
    </button>
</div>

{{-- Checklist Cards --}}
@if ($checklists->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
        <i class="bx bx-check-shield text-6xl text-txt-secondary"></i>
        <h3 class="text-xl font-extrabold mt-4">No checklists yet</h3>
        <p class="text-txt-secondary mt-2">Create your first deployment checklist</p>
        <button type="button" onclick="openQcModal()"
            class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
            + New Checklist
        </button>
    </div>
@else
    <div class="space-y-6">
    @foreach ($checklists as $checklist)
        @php
            $total   = $checklist->items->count();
            $checked = $checklist->items->where('is_checked', true)->count();
            $pct     = $total > 0 ? round(($checked / $total) * 100) : 0;
            $readiness = $pct >= 60 ? ['label'=>'Ready','class'=>'bg-[#22C55E] text-white']
                       : ($pct >= 30 ? ['label'=>'Almost Ready','class'=>'bg-yellow-acc text-[#111827]']
                                     : ['label'=>'Not Ready','class'=>'bg-gray-200 text-txt-secondary']);
        @endphp
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            {{-- Header --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-12 h-12 rounded-button flex items-center justify-center flex-shrink-0 bg-primary/10">
                        <i class="bx bx-list-check text-primary text-[24px]"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base">{{ $checklist->title }}</h3>
                        <p class="text-xs text-txt-secondary mt-0.5">{{ $total }} items</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-3 py-1 border-2 border-border-dark rounded-full flex-shrink-0 {{ $readiness['class'] }}">
                    {{ $readiness['label'] }}
                </span>
            </div>

            {{-- Progress --}}
            <div class="mb-4">
                <div class="w-full h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                    <div class="h-full bg-[#22C55E] rounded-full transition-all duration-300" style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-xs text-txt-secondary mt-1">{{ $checked }} / {{ $total }} items checked ({{ $pct }}%)</p>
            </div>

            {{-- Items --}}
            <div class="space-y-2 mb-4">
                @foreach ($checklist->items as $item)
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-button border-2 border-border-dark {{ $item->is_checked ? 'bg-[#22C55E]/5' : '' }}">
                        <form method="POST" action="{{ route('quality-control.toggle-checked', $item) }}" class="inline">
                            @csrf @method('PATCH')
                            <input type="checkbox" name="is_checked" onchange="this.closest('form').submit()"
                                {{ $item->is_checked ? 'checked' : '' }}
                                class="w-4 h-4 accent-[#22C55E] cursor-pointer flex-shrink-0">
                        </form>
                        <span class="text-sm font-medium {{ $item->is_checked ? 'line-through text-txt-secondary' : '' }}">
                            {{ $item->label }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t-4 border-border-dark">
                <button type="button"
                    onclick="openQcModal({{ $checklist->id }}, {{ json_encode($checklist->title) }}, {{ json_encode($checklist->items->pluck('label')) }})"
                    class="flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    <i class="bx bx-edit text-base"></i> Edit
                </button>
                <form id="del-qc-{{ $checklist->id }}" action="{{ route('quality-control.destroy', $checklist) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="button"
                        onclick="deleteChecklist('del-qc-{{ $checklist->id }}', {{ json_encode($checklist->title) }})"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 hover:bg-danger/10 hover:text-danger transition-all duration-200 ease-out">
                        <i class="bx bx-trash text-base"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    @endforeach
    </div>
@endif

{{-- QC Modal --}}
<div id="qc-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onclick="if(event.target===this)closeQcModal()">
    <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg max-h-[90vh] overflow-y-auto animate-scale-in" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark sticky top-0 bg-surface">
            <h3 id="qc-modal-title" class="text-lg font-extrabold">New Checklist</h3>
            <button onclick="closeQcModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors"><i class="bx bx-x"></i></button>
        </div>
        <form id="qc-form" method="POST" action="{{ route('quality-control.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="qc-method" value="POST">
            <div>
                <label class="block text-sm font-semibold mb-1.5">Checklist Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="qc-title" required placeholder="e.g. Deployment Checklist v1.0"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Checklist Items</label>
                <div id="qc-items-list" class="space-y-2 mb-2"></div>
                <button type="button" onclick="addQcItem()"
                    class="mt-1 text-sm font-semibold text-primary hover:text-primary/70 transition-colors flex items-center gap-1">
                    <i class="bx bx-plus"></i> Add item
                </button>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeQcModal()"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Cancel</button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Save Checklist</button>
            </div>
        </form>
    </div>
</div>

<script>
const qcStoreUrl  = '{{ route('quality-control.store') }}';
const qcUpdateUrl = '{{ url('quality-control') }}';

function openQcModal(id, title, items) {
    const isEdit = !!id;
    document.getElementById('qc-modal-title').textContent = isEdit ? 'Edit Checklist' : 'New Checklist';
    document.getElementById('qc-method').value = isEdit ? 'PUT' : 'POST';
    document.getElementById('qc-form').action  = isEdit ? qcUpdateUrl+'/'+id : qcStoreUrl;
    document.getElementById('qc-title').value  = title || '';

    // Rebuild items list
    document.getElementById('qc-items-list').innerHTML = '';
    (items || []).forEach(label => addQcItem(label));

    document.getElementById('qc-modal').classList.remove('hidden');
}

function closeQcModal() { document.getElementById('qc-modal').classList.add('hidden'); }

function addQcItem(value) {
    const list  = document.getElementById('qc-items-list');
    const idx   = list.children.length;
    const row   = document.createElement('div');
    row.className = 'flex items-center gap-2';
    row.innerHTML = `
        <input type="text" name="items[${idx}][label]" value="${(value||'').replace(/"/g,'&quot;')}"
            placeholder="Add item..."
            class="flex-1 px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
        <button type="button" onclick="this.closest('div').remove()"
            class="p-2 text-danger hover:bg-danger/10 rounded-button transition-colors">
            <i class="bx bx-x text-xl"></i>
        </button>`;
    list.appendChild(row);
    if (!value) row.querySelector('input').focus();
}

function deleteChecklist(formId, title) {
    Swal.fire({
        title: 'Hapus Checklist?',
        text: '"' + title + '" akan dihapus permanen beserta semua itemnya.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#EF4444', cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal',
        background: '#FFFFFF', customClass: { popup: 'border-4 border-border-dark rounded-modal shadow-hard' }
    }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}
</script>
@endsection
