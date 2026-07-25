{{-- Logo --}}
<div class="px-6 py-6 border-b-4 border-border-dark">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <div class="w-10 h-10 bg-primary rounded-button flex items-center justify-center shadow-hard">
            <span class="text-white font-extrabold text-lg">U</span>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-txt-primary leading-none">UROO.DEV</h1>
            <p class="text-xs text-txt-secondary font-medium">Workspace</p>
        </div>
    </a>
</div>

{{-- Navigation --}}
<nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
    @php
        $menuItems = [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bx bx-grid-alt'],
            ['route' => 'github', 'label' => 'GitHub Monitor', 'icon' => 'bx bxl-github'],
            ['route' => 'projects', 'label' => 'Projects', 'icon' => 'bx bx-folder'],
            ['route' => 'credentials', 'label' => 'Credential Vault', 'icon' => 'bx bx-lock-alt'],
            ['route' => 'clients', 'label' => 'Clients', 'icon' => 'bx bx-user'],
            ['route' => 'invoices', 'label' => 'Invoices', 'icon' => 'bx bx-receipt'],
            ['route' => 'notes', 'label' => 'Developer Notes', 'icon' => 'bx bx-note'],
            ['route' => 'bookmarks', 'label' => 'Bookmarks', 'icon' => 'bx bx-bookmark'],
            ['route' => 'quality-control', 'label' => 'Quality Control', 'icon' => 'bx bx-check-shield'],
            ['route' => 'ideas', 'label' => 'App Ideas', 'icon' => 'bx bx-bulb'],
            ['route' => 'brain-dump', 'label' => 'Brain Dump', 'icon' => 'bx bx-cloud'],
            ['route' => 'savings', 'label' => 'Savings Vault', 'icon' => 'bx bx-wallet'],
            ['route' => 'subscriptions', 'label' => 'Subscriptions', 'icon' => 'bx bx-calendar'],
        ];
        $currentRoute = Route::currentRouteName() ?? 'dashboard';
    @endphp

    @foreach ($menuItems as $item)
        <a href="{{ route($item['route']) }}"
            class="flex items-center gap-3 px-4 py-3 rounded-button text-sm font-semibold transition-all duration-200 ease-out
            {{ $currentRoute === $item['route']
                ? 'bg-primary text-white shadow-hard'
                : 'text-txt-primary hover:bg-gray-100 hover:-translate-y-0.5' }}">
            <i class="{{ $item['icon'] }} text-[22px]"></i>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>

{{-- Bottom Section --}}
<div class="border-t-4 border-border-dark px-4 py-4">
    <a href="{{ route('logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
        class="flex items-center gap-3 px-4 py-3 rounded-button text-sm font-semibold text-danger hover:bg-red-50 transition-all duration-200 ease-out">
        <i class="bx bx-log-out text-[22px]"></i>
        <span>Logout</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</div>