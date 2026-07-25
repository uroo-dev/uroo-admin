@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-folder text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">12</p>
                    <p class="text-sm font-medium text-txt-secondary">Active Projects</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-receipt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">5</p>
                    <p class="text-sm font-medium text-txt-secondary">Pending Invoices</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-user text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">8</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Clients</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-acc rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-wallet text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp 2.5Jt</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Savings</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Recent Activities --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-extrabold">Recent Activities</h3>
                <i class="bx bx-time text-txt-secondary text-[22px]"></i>
            </div>
            <div class="space-y-4">
                <div class="flex items-start gap-4 pb-4 border-b-2 border-gray-100">
                    <div class="w-10 h-10 bg-primary/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bx-git-branch text-primary text-[20px]"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Push ke branch <span class="text-primary">main</span></p>
                        <p class="text-xs text-txt-secondary mt-0.5">30 menit lalu</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 pb-4 border-b-2 border-gray-100">
                    <div class="w-10 h-10 bg-[#22C55E]/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bx-check-circle text-[#22C55E] text-[20px]"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Invoice #INV-001 telah dibayar</p>
                        <p class="text-xs text-txt-secondary mt-0.5">2 jam lalu</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 pb-4 border-b-2 border-gray-100">
                    <div class="w-10 h-10 bg-[#F59E0B]/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bx-cloud-upload text-[#F59E0B] text-[20px]"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Deploy ke production berhasil</p>
                        <p class="text-xs text-txt-secondary mt-0.5">5 jam lalu</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-purple-acc/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bx-user-plus text-purple-acc text-[20px]"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Client baru: PT Maju Jaya</p>
                        <p class="text-xs text-txt-secondary mt-0.5">Kemarin</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-extrabold">Quick Actions</h3>
                <i class="bx bx-zap text-txt-secondary text-[22px]"></i>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <a href="#" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-folder-open text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">New Project</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-receipt text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">New Invoice</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-lock-alt text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">Add Credential</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-3 p-5 rounded-button border-4 border-border-dark bg-primary/5 hover:bg-primary hover:text-white transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard group">
                    <i class="bx bx-note text-[28px] text-primary group-hover:text-white transition-colors"></i>
                    <span class="text-sm font-bold">Quick Note</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Status Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-3 mb-4">
                <i class="bx bx-git-branch text-[22px] text-primary"></i>
                <h3 class="font-extrabold">GitHub Activity</h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Commits hari ini</span>
                    <span class="text-sm font-extrabold">7</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Open PR</span>
                    <span class="text-sm font-extrabold">3</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Branches</span>
                    <span class="text-sm font-extrabold">5</span>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-3 mb-4">
                <i class="bx bx-check-shield text-[22px] text-[#22C55E]"></i>
                <h3 class="font-extrabold">Quality Control</h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Checked</span>
                    <span class="text-sm font-extrabold">6/10</span>
                </div>
                <div class="w-full h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                    <div class="h-full bg-[#22C55E] rounded-full transition-all duration-500" style="width: 60%"></div>
                </div>
                <p class="text-xs text-txt-secondary">Deploy readiness: <span class="font-bold text-[#F59E0B]">60%</span></p>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-3 mb-4">
                <i class="bx bx-bell text-[22px] text-[#F59E0B]"></i>
                <h3 class="font-extrabold">Notifications</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-[#EF4444]"></div>
                    <span class="text-sm font-medium">SSL akan expired dalam 7 hari</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-[#F59E0B]"></div>
                    <span class="text-sm font-medium">Invoice #INV-003 belum dibayar</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-[#22C55E]"></div>
                    <span class="text-sm font-medium">Deploy production berhasil</span>
                </div>
            </div>
        </div>
    </div>
@endsection