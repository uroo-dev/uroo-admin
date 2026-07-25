@auth
<div class="h-full px-6 flex items-center justify-between">
    {{-- Left: Mobile Menu + Title --}}
    <div class="flex items-center gap-4">
        <button class="lg:hidden text-2xl text-txt-primary hover:text-primary transition-colors"
            x-on:click="sidebarOpen = !sidebarOpen">
            <i class="bx bx-menu"></i>
        </button>
        <h2 class="text-lg font-bold text-txt-primary hidden sm:block">
            @yield('page-title', 'Dashboard')
        </h2>
    </div>

    {{-- Right: Search + Notifications + Profile --}}
    <div class="flex items-center gap-3">
        {{-- Search --}}
        <div class="relative hidden md:block" x-data="{ searchOpen: false }">
            <button @click="searchOpen = !searchOpen"
                class="w-10 h-10 flex items-center justify-center rounded-button border-4 border-border-dark bg-surface text-txt-primary hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <i class="bx bx-search text-[22px]"></i>
            </button>
            <div x-show="searchOpen" @click.outside="searchOpen = false"
                class="absolute right-0 top-12 w-72 bg-surface border-4 border-border-dark rounded-card shadow-hard p-3 animate-scale-in"
                style="display: none;">
                <input type="text" placeholder="Cari sesuatu..."
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
            </div>
        </div>

        {{-- Notifications --}}
        <div class="relative" x-data="{ notifOpen: false }">
            <button @click="notifOpen = !notifOpen"
                class="w-10 h-10 flex items-center justify-center rounded-button border-4 border-border-dark bg-surface text-txt-primary hover:-translate-y-0.5 transition-all duration-200 ease-out relative">
                <i class="bx bx-bell text-[22px]"></i>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-danger text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-surface">3</span>
            </button>
            <div x-show="notifOpen" @click.outside="notifOpen = false"
                class="absolute right-0 top-12 w-80 bg-surface border-4 border-border-dark rounded-card shadow-hard animate-scale-in"
                style="display: none;">
                <div class="p-4 border-b-4 border-border-dark">
                    <h3 class="font-bold">Notifikasi</h3>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <div class="p-4 border-b-2 border-gray-100 hover:bg-gray-50 cursor-pointer">
                        <p class="text-sm font-semibold">Invoice baru dari Client A</p>
                        <p class="text-xs text-txt-secondary">2 menit lalu</p>
                    </div>
                    <div class="p-4 border-b-2 border-gray-100 hover:bg-gray-50 cursor-pointer">
                        <p class="text-sm font-semibold">Deploy berhasil</p>
                        <p class="text-xs text-txt-secondary">1 jam lalu</p>
                    </div>
                    <div class="p-4 hover:bg-gray-50 cursor-pointer">
                        <p class="text-sm font-semibold">SSL certificate akan expired</p>
                        <p class="text-xs text-txt-secondary">3 hari lagi</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile --}}
        <div class="relative" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen"
                class="flex items-center gap-2 px-3 py-2 rounded-button border-4 border-border-dark bg-surface hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="text-sm font-semibold text-txt-primary hidden sm:block">{{ auth()->user()->name }}</span>
                <i class="bx bx-chevron-down text-txt-secondary"></i>
            </button>
            <div x-show="profileOpen" @click.outside="profileOpen = false"
                class="absolute right-0 top-14 w-56 bg-surface border-4 border-border-dark rounded-card shadow-hard animate-scale-in"
                style="display: none;">
                <div class="p-4 border-b-4 border-border-dark">
                    <p class="font-bold text-sm">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-txt-secondary">{{ auth()->user()->email }}</p>
                </div>
                <div class="p-2">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-button text-sm font-semibold hover:bg-gray-100 transition-colors">
                        <i class="bx bx-user-circle text-[20px]"></i> Profile
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-button text-sm font-semibold hover:bg-gray-100 transition-colors">
                        <i class="bx bx-cog text-[20px]"></i> Settings
                    </a>
                    <hr class="my-2 border-2 border-gray-100">
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="flex items-center gap-3 px-3 py-2 rounded-button text-sm font-semibold text-danger hover:bg-red-50 transition-colors">
                        <i class="bx bx-log-out text-[20px]"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth