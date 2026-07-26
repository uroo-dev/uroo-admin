@extends('layouts.app')

@section('title', 'Brain Dump')
@section('page-title', 'Brain Dump')

@section('content')
<div x-data="{
    confirmDelete(formId) {
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
    }
}">

    {{-- Quick Add Section --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <h2 class="text-lg font-extrabold mb-4">Quick Add</h2>
        <form method="POST" action="{{ route('brain-dumps.store') }}" class="flex gap-4 items-start">
            @csrf
            <textarea name="content" rows="3" required
                class="flex-1 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                placeholder="Type your thought..."></textarea>
            <button type="submit"
                class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out self-end">
                Add
            </button>
        </form>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <form method="GET" action="{{ route('brain-dumps.index') }}">
            <div class="relative flex-1 max-w-md">
                <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search brain dumps..."
                    class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <button type="submit" class="px-4 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out mt-4 sm:mt-0">
                Search
            </button>
        </form>
    </div>

    {{-- Pinned Section --}}
    @if ($pinnedDumps->isNotEmpty())
        <h2 class="text-xl font-extrabold mb-4 flex items-center gap-2">
            <i class="bx bx-pinch text-primary text-2xl"></i>
            Pinned
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @foreach ($pinnedDumps as $dump)
                <div class="bg-primary/10 border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover flex flex-col">
                    <p class="text-sm text-txt-primary line-clamp-3 mb-4 flex-1">{{ $dump->content }}</p>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('brain-dumps.toggle-pin', $dump) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 rounded transition-colors text-primary bg-primary/20 hover:bg-primary/30" title="Unpin">
                                <i class="bx bx-pin text-lg"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('brain-dumps.toggle-archive', $dump) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 rounded transition-colors text-txt-secondary hover:bg-gray-100" title="Archive">
                                <i class="bx bx-archive text-lg"></i>
                            </button>
                        </form>
                        <form id="delete-form-dump-{{ $dump->id }}" action="{{ route('brain-dumps.destroy', $dump) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="confirmDelete('delete-form-dump-{{ $dump->id }}')"
                                class="p-2 rounded transition-colors text-txt-secondary hover:text-danger hover:bg-red-50" title="Delete">
                                <i class="bx bx-trash text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- All Notes Section --}}
    <h2 class="text-xl font-extrabold mb-4 flex items-center gap-2">
        <i class="bx bx-note text-primary text-2xl"></i>
        All Notes
    </h2>

    @if ($dumps->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-brain text-6xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">No brain dumps yet</h3>
            <p class="text-txt-secondary mt-2">Type your thoughts in the quick add section above</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach ($dumps as $dump)
                <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover flex flex-col">
                    <p class="text-sm text-txt-primary line-clamp-3 mb-4 flex-1">{{ $dump->content }}</p>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('brain-dumps.toggle-pin', $dump) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 rounded transition-colors text-txt-secondary hover:bg-primary/10 hover:text-primary" title="Pin">
                                <i class="bx bx-pin text-lg"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('brain-dumps.toggle-archive', $dump) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 rounded transition-colors text-txt-secondary hover:bg-gray-100" title="Archive">
                                <i class="bx bx-archive text-lg"></i>
                            </button>
                        </form>
                        <form id="delete-form-dump-{{ $dump->id }}" action="{{ route('brain-dumps.destroy', $dump) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="confirmDelete('delete-form-dump-{{ $dump->id }}')"
                                class="p-2 rounded transition-colors text-txt-secondary hover:text-danger hover:bg-red-50" title="Delete">
                                <i class="bx bx-trash text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        {{ $dumps->links() }}
    @endif

</div>
@endsection