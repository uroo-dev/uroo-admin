@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('projects.index') }}" class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover block">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-folder text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['active_projects'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Active Projects</p>
                </div>
            </div>
        </a>

        <a href="{{ route('invoices.index') }}" class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover block">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-receipt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['pending_invoices'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Pending Invoices</p>
                </div>
            </div>
        </a>

        <a href="{{ route('clients.index') }}" class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover block">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-user text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['total_clients'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Clients</p>
                </div>
            </div>
        </a>

        <a href="{{ route('savings.index') }}" class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover block">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-acc rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-wallet text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ formatRupiah($stats['total_savings']) }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Savings</p>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Recent Activities --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-extrabold">Recent Activities</h3>
                <i class="bx bx-time text-txt-secondary text-[22px]"></i>
            </div>
            <div class="space-y-4">
                @foreach ($recentActivities as $activity)
                    @if ($activity['type'] === 'commit')
                        <a href="{{ route('github') }}" class="flex items-start gap-4 pb-4 border-b-2 border-gray-100 group">
                            <div class="w-10 h-10 bg-primary/10 rounded-button flex items-center justify-center flex-shrink-0">
                                <i class="bx bx-git-commit text-primary text-[20px]"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold truncate group-hover:text-primary">{{ $activity['message'] }}</p>
                                <p class="text-xs text-txt-secondary mt-0.5">
                                    {{ $activity['repo'] ?? 'GitHub' }} · {{ $activity['date']?->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                    @elseif ($activity['type'] === 'invoice')
                        <a href="{{ route('invoices.index') }}" class="flex items-start gap-4 pb-4 border-b-2 border-gray-100 group">
                            <div class="w-10 h-10 {{ $activity['paid'] ? 'bg-[#22C55E]/10' : 'bg-[#F59E0B]/10' }} rounded-button flex items-center justify-center flex-shrink-0">
                                <i class="bx bx-receipt {{ $activity['paid'] ? 'text-[#22C55E]' : 'text-[#F59E0B]' }} text-[20px]"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold truncate group-hover:text-primary">
                                    Invoice <span class="font-extrabold">#{{ $activity['number'] }}</span> {{ $activity['paid'] ? 'dibayar' : 'menunggu pembayaran' }}
                                </p>
                                <p class="text-xs text-txt-secondary mt-0.5">{{ $activity['client'] ?? 'N/A' }} · {{ formatRupiah($activity['total']) }} · {{ $activity['date']?->diffForHumans() }}</p>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('clients.index') }}" class="flex items-start gap-4 pb-4 border-b-2 border-gray-100 group">
                            <div class="w-10 h-10 bg-purple-acc/10 rounded-button flex items-center justify-center flex-shrink-0">
                                <i class="bx bx-user-plus text-purple-acc text-[20px]"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold truncate group-hover:text-primary">Client baru: {{ $activity['name'] }}</p>
                                <p class="text-xs text-txt-secondary mt-0.5">{{ $activity['company'] ?? '—' }} · {{ $activity['date']?->diffForHumans() }}</p>
                            </div>
                        </a>
                    @endif
                @endforeach

                @if (empty($recentActivities))
                    <div class="text-center py-8">
                        <i class="bx bx-history text-4xl text-txt-secondary"></i>
                        <p class="text-txt-secondary mt-2 text-sm">Belum ada aktivitas</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-extrabold">Quick Actions</h3>
                <i class="bx bx-zap text-txt-secondary text-[22px]"></i>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('projects.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-folder-open text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">Manage Projects</span>
                </a>
                <a href="{{ route('invoices.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-receipt text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">New Invoice</span>
                </a>
                <a href="{{ route('credentials.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-lock-alt text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">Credential Vault</span>
                </a>
                <a href="{{ route('notes.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-note text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">New Note</span>
                </a>
                <a href="{{ route('clients.index') }}" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-user text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">Add Client</span>
                </a>
                <a href="{{ route('github') }}" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bxl-github text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">GitHub Sync</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Status Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- GitHub Activity --}}
        <a href="{{ route('github') }}" class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover block">
            <div class="flex items-center gap-3 mb-4">
                <i class="bx bxl-github text-[22px] text-primary"></i>
                <h3 class="font-extrabold">GitHub Activity</h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Repositories</span>
                    <span class="text-sm font-extrabold">{{ $githubStats['repos'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Commits hari ini</span>
                    <span class="text-sm font-extrabold">{{ $githubStats['commitsToday'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Branches</span>
                    <span class="text-sm font-extrabold">{{ $githubStats['branches'] }}</span>
                </div>
            </div>
        </a>

        {{-- Quality Control --}}
        @php
            $qcTotal = $qualityChecklists->count();
            $qcReady = $qualityChecklists->where('readiness', 'ready')->count();
            $qcProgress = $qcTotal > 0 ? $qualityChecklists->avg('progress') : 0;
        @endphp
        <a href="{{ route('quality-control') }}" class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover block">
            <div class="flex items-center gap-3 mb-4">
                <i class="bx bx-check-shield text-[22px] text-[#22C55E]"></i>
                <h3 class="font-extrabold">Quality Control</h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Checklist siap deploy</span>
                    <span class="text-sm font-extrabold">{{ $qcReady }}/{{ $qcTotal }}</span>
                </div>
                <div class="w-full h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                    <div class="h-full bg-[#22C55E] rounded-full transition-all duration-500" style="width: {{ min(100, (int) $qcProgress) }}%"></div>
                </div>
                <p class="text-xs text-txt-secondary">Deploy readiness: <span class="font-bold text-[#F59E0B]">{{ (int) $qcProgress }}%</span></p>
            </div>
        </a>

        {{-- Overdue Invoices / Notifications --}}
        <a href="{{ route('invoices.index') }}" class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover block">
            <div class="flex items-center gap-3 mb-4">
                <i class="bx bx-bell text-[22px] text-danger"></i>
                <h3 class="font-extrabold">Invoice Menunggu</h3>
            </div>
            @if ($overdueInvoices)
                <div class="space-y-3">
                    @foreach ($overdueInvoices as $inv)
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-[#EF4444] flex-shrink-0"></div>
                            <p class="text-sm font-medium truncate">#{{ $inv['number'] }} · {{ $inv['client'] ?? 'N/A' }}</p>
                            <span class="text-xs font-bold text-danger ml-auto flex-shrink-0">{{ formatRupiah($inv['paid'] ? $inv['total'] - $inv['paid'] : $inv['total']) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-[#22C55E] flex-shrink-0"></div>
                    <p class="text-sm font-medium">Tidak ada invoice yang lewat jatuh tempo.</p>
                </div>
            @endif
        </a>
    </div>
@endsection