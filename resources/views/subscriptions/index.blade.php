@extends('layouts.app')

@section('title', 'Subscriptions')
@section('page-title', 'Subscriptions')

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-calendar text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp 1.2Jt</p>
                    <p class="text-sm font-medium text-txt-secondary">Monthly Total</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-calendar-check text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp 14.4Jt</p>
                    <p class="text-sm font-medium text-txt-secondary">Annual Total</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-check-shield text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">6</p>
                    <p class="text-sm font-medium text-txt-secondary">Active</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-time text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">2</p>
                    <p class="text-sm font-medium text-txt-secondary">Upcoming</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div class="flex flex-wrap items-center gap-3">
            <select class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                <option value="">All Categories</option>
                <option value="saas">SaaS</option>
                <option value="hosting">Hosting</option>
                <option value="domain">Domain</option>
                <option value="tools">Tools</option>
                <option value="entertainment">Entertainment</option>
            </select>
            <select class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="unpaid">Unpaid</option>
                <option value="cancelled">Cancelled</option>
                <option value="upcoming">Upcoming</option>
            </select>
        </div>
        <x-button @click="$dispatch('open-modal', { id: 'subscription-modal' })">
            <i class="bx bx-plus"></i> Add Subscription
        </x-button>
    </div>

    {{-- Subscriptions List --}}
    <div class="space-y-4">
        <x-card>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 bg-secondary/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bxl-github text-secondary text-[24px]"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-extrabold truncate">GitHub Team</h3>
                        <p class="text-xs text-txt-secondary font-medium">github.com</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-sm font-extrabold">Rp 120rb</p>
                        <p class="text-[11px] text-txt-secondary font-semibold">/month</p>
                    </div>
                    <x-badge variant="info">SaaS</x-badge>
                    <x-badge variant="success">Active</x-badge>
                    <div class="text-center">
                        <p class="text-xs font-semibold text-txt-secondary">Due: 25 Jul 2026</p>
                    </div>
                    <x-button variant="success" size="sm">
                        <i class="bx bx-check"></i> Paid
                    </x-button>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 bg-[#22C55E]/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bx-cloud text-[#22C55E] text-[24px]"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-extrabold truncate">DigitalOcean Droplet</h3>
                        <p class="text-xs text-txt-secondary font-medium">digitalocean.com</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-sm font-extrabold">Rp 240rb</p>
                        <p class="text-[11px] text-txt-secondary font-semibold">/month</p>
                    </div>
                    <x-badge variant="info">Hosting</x-badge>
                    <x-badge variant="success">Active</x-badge>
                    <div class="text-center">
                        <p class="text-xs font-semibold text-txt-secondary">Due: 1 Aug 2026</p>
                    </div>
                    <x-button variant="success" size="sm">
                        <i class="bx bx-check"></i> Paid
                    </x-button>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 bg-primary/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bx-globe text-primary text-[24px]"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-extrabold truncate">Namecheap Domain</h3>
                        <p class="text-xs text-txt-secondary font-medium">namecheap.com</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-sm font-extrabold">Rp 350rb</p>
                        <p class="text-[11px] text-txt-secondary font-semibold">/year</p>
                    </div>
                    <x-badge variant="info">Domain</x-badge>
                    <x-badge variant="danger">Unpaid</x-badge>
                    <div class="text-center">
                        <p class="text-xs font-semibold text-txt-secondary">Due: 10 Aug 2026</p>
                    </div>
                    <x-button variant="danger" size="sm">
                        <i class="bx bx-x"></i> Unpaid
                    </x-button>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 bg-purple-acc/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bxl-figma text-purple-acc text-[24px]"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-extrabold truncate">Figma Professional</h3>
                        <p class="text-xs text-txt-secondary font-medium">figma.com</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-sm font-extrabold">Rp 280rb</p>
                        <p class="text-[11px] text-txt-secondary font-semibold">/month</p>
                    </div>
                    <x-badge variant="info">Tools</x-badge>
                    <x-badge variant="success">Active</x-badge>
                    <div class="text-center">
                        <p class="text-xs font-semibold text-txt-secondary">Due: 15 Aug 2026</p>
                    </div>
                    <x-button variant="success" size="sm">
                        <i class="bx bx-check"></i> Paid
                    </x-button>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 bg-[#F59E0B]/10 rounded-button flex items-center justify-center flex-shrink-0">
                        <i class="bx bxl-netflix text-[#F59E0B] text-[24px]"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-extrabold truncate">Netflix Premium</h3>
                        <p class="text-xs text-txt-secondary font-medium">netflix.com</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-sm font-extrabold">Rp 186rb</p>
                        <p class="text-[11px] text-txt-secondary font-semibold">/month</p>
                    </div>
                    <x-badge variant="info">Entertainment</x-badge>
                    <x-badge variant="warning">Upcoming</x-badge>
                    <div class="text-center">
                        <p class="text-xs font-semibold text-txt-secondary">Starts: 1 Sep 2026</p>
                    </div>
                    <x-button variant="success" size="sm">
                        <i class="bx bx-check"></i> Paid
                    </x-button>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Create/Edit Modal --}}
    <x-modal id="subscription-modal" title="Add Subscription">
        <form class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <x-input name="sub_name" label="Name" placeholder="e.g. GitHub Team" />
                <x-input name="sub_provider" label="Provider" placeholder="e.g. github.com" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-input name="sub_cost" label="Cost" type="number" placeholder="e.g. 120000" />
                <div class="space-y-1.5">
                    <label for="sub_billing" class="block text-sm font-semibold text-txt-primary">Billing Cycle</label>
                    <select name="sub_billing" id="sub_billing"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="quarterly">Quarterly</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="sub_category" class="block text-sm font-semibold text-txt-primary">Category</label>
                    <select name="sub_category" id="sub_category"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="saas">SaaS</option>
                        <option value="hosting">Hosting</option>
                        <option value="domain">Domain</option>
                        <option value="tools">Tools</option>
                        <option value="entertainment">Entertainment</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="sub_status" class="block text-sm font-semibold text-txt-primary">Status</label>
                    <select name="sub_status" id="sub_status"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="active">Active</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="upcoming">Upcoming</option>
                    </select>
                </div>
            </div>
            <x-input name="sub_due" label="Due Date" type="date" />
            <div class="flex items-center justify-end gap-3 pt-2">
                <x-button variant="secondary" @click="$dispatch('close-modal', { id: 'subscription-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <i class="bx bx-save"></i> Save Subscription
                </x-button>
            </div>
        </form>
    </x-modal>
@endsection
