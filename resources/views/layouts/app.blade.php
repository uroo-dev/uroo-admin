<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'UROO.DEV')) — {{ config('app.name') }}</title>

    {{-- Inter Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- BoxIcons --}}
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#3B82F6',
                        'pink-acc': '#FF66C4',
                        'yellow-acc': '#FFD93D',
                        'cyan-acc': '#67E8F9',
                        'purple-acc': '#A855F7',
                        surface: '#FFFFFF',
                        bgmain: '#F8F8F8',
                        'border-dark': '#111827',
                        'txt-primary': '#111827',
                        'txt-secondary': '#6B7280',
                    },
                    borderRadius: {
                        card: '20px',
                        button: '18px',
                        input: '16px',
                        modal: '24px',
                    },
                    boxShadow: {
                        'hard': '8px 8px 0px #111827',
                        'hard-hover': '10px 10px 0px #111827',
                        'hard-pressed': '4px 4px 0px #111827',
                        'hard-sm': '4px 4px 0px #111827',
                    },
                    animation: {
                        'slide-in': 'slideIn 200ms ease-out',
                        'scale-in': 'scaleIn 200ms ease-out',
                        'fade-in': 'fadeIn 200ms ease-out',
                        'slide-right': 'slideRight 200ms ease-out',
                    },
                    keyframes: {
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.95)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideRight: {
                            '0%': { transform: 'translateX(100%)' },
                            '100%': { transform: 'translateX(0)' },
                        },
                    },
                },
            },
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans bg-bgmain text-txt-primary antialiased">

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="w-[280px] bg-surface border-r-4 border-border-dark flex-shrink-0 hidden lg:flex flex-col z-30"
            x-data="{ sidebarOpen: true }"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            @include('layouts.sidebar')
        </aside>

        {{-- Mobile Sidebar Overlay --}}
        <div class="fixed inset-0 bg-black/50 z-20 lg:hidden"
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
        </div>

        {{-- Mobile Sidebar Drawer --}}
        <aside class="fixed top-0 left-0 w-[280px] h-full bg-surface border-r-4 border-border-dark z-30 lg:hidden"
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            style="display: none;">
            @include('layouts.sidebar')
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Navbar --}}
            <header class="h-20 bg-surface border-b-4 border-border-dark flex-shrink-0 sticky top-0 z-10">
                @include('layouts.navbar')
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-[1440px] mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    {{-- Global SweetAlert Flash --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal:success', (data) => {
                Swal.fire({
                    icon: 'success',
                    title: data[0].title || 'Berhasil!',
                    text: data[0].text || '',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    background: '#FFFFFF',
                    customClass: {
                        popup: 'border-4 border-border-dark rounded-card shadow-hard'
                    }
                });
            });

            Livewire.on('swal:error', (data) => {
                Swal.fire({
                    icon: 'error',
                    title: data[0].title || 'Gagal!',
                    text: data[0].text || '',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    background: '#FFFFFF',
                    customClass: {
                        popup: 'border-4 border-border-dark rounded-card shadow-hard'
                    }
                });
            });

            Livewire.on('swal:confirm', (data) => {
                Swal.fire({
                    title: data[0].title || 'Apakah kamu yakin?',
                    text: data[0].text || '',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: data[0].confirmText || 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    background: '#FFFFFF',
                    customClass: {
                        popup: 'border-4 border-border-dark rounded-modal shadow-hard'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(data[0].event || 'confirm:delete');
                    }
                });
            });
        });
    </script>
</body>
</html>