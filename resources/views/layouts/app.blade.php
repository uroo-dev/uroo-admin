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

    <div class="flex h-screen overflow-hidden"
        x-data="{ sidebarOpen: false }"
        @keydown.window.escape="sidebarOpen = false">
        {{-- Desktop Sidebar --}}
        <aside class="w-[280px] bg-surface border-r-4 border-border-dark flex-shrink-0 hidden lg:flex flex-col z-30">
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
            @click="sidebarOpen = false"
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
            <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                <div class="max-w-[1440px] mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    {{-- ═══════════════════════════════════════════════════════════
         Global SweetAlert2 — Neo Brutalism Design System
         Sesuai DESIGN.MD: border hitam 4px, hard shadow, flat color,
         font Inter, no blur, no gradient, radius card/modal/button
    ═══════════════════════════════════════════════════════════ --}}
    <style>
        /* ── Base popup ─────────────────────────────────────── */
        .swal-neo .swal2-popup {
            font-family: 'Inter', sans-serif !important;
            background: #FFFFFF !important;
            border: 4px solid #111827 !important;
            border-radius: 24px !important;
            box-shadow: 8px 8px 0px #111827 !important;
            padding: 2rem !important;
        }

        /* ── Toast popup ────────────────────────────────────── */
        .swal-neo-toast .swal2-popup {
            font-family: 'Inter', sans-serif !important;
            background: #FFFFFF !important;
            border: 4px solid #111827 !important;
            border-radius: 20px !important;
            box-shadow: 6px 6px 0px #111827 !important;
            padding: 0.75rem 1.25rem !important;
        }

        /* ── Title ──────────────────────────────────────────── */
        .swal-neo .swal2-title,
        .swal-neo-toast .swal2-title {
            font-family: 'Inter', sans-serif !important;
            font-weight: 800 !important;
            font-size: 1.125rem !important;
            color: #111827 !important;
            padding: 0 !important;
            margin-bottom: 0.25rem !important;
        }

        /* ── Content text ───────────────────────────────────── */
        .swal-neo .swal2-html-container,
        .swal-neo-toast .swal2-html-container {
            font-family: 'Inter', sans-serif !important;
            font-weight: 500 !important;
            font-size: 0.875rem !important;
            color: #6B7280 !important;
            margin: 0.25rem 0 0 0 !important;
        }

        /* ── Icon colors (flat, no shadow) ──────────────────── */
        .swal-neo .swal2-icon,
        .swal-neo-toast .swal2-icon {
            border-width: 3px !important;
            box-shadow: none !important;
        }
        .swal-neo .swal2-icon.swal2-success,
        .swal-neo-toast .swal2-icon.swal2-success {
            border-color: #22C55E !important;
            color: #22C55E !important;
        }
        .swal-neo .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #22C55E !important;
        }
        .swal-neo .swal2-icon.swal2-success .swal2-success-ring {
            border-color: #22C55E4D !important;
        }
        .swal-neo .swal2-icon.swal2-error,
        .swal-neo-toast .swal2-icon.swal2-error {
            border-color: #EF4444 !important;
            color: #EF4444 !important;
        }
        .swal-neo .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #EF4444 !important;
        }
        .swal-neo .swal2-icon.swal2-warning,
        .swal-neo-toast .swal2-icon.swal2-warning {
            border-color: #F59E0B !important;
            color: #F59E0B !important;
        }
        .swal-neo .swal2-icon.swal2-info {
            border-color: #4F46E5 !important;
            color: #4F46E5 !important;
        }

        /* ── Confirm button ─────────────────────────────────── */
        .swal-neo .swal2-confirm {
            font-family: 'Inter', sans-serif !important;
            font-weight: 700 !important;
            font-size: 0.875rem !important;
            border-radius: 18px !important;
            border: 4px solid #111827 !important;
            box-shadow: 4px 4px 0px #111827 !important;
            padding: 0.625rem 1.5rem !important;
            transition: transform 150ms ease-out, box-shadow 150ms ease-out !important;
        }
        .swal-neo .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 6px 6px 0px #111827 !important;
        }
        .swal-neo .swal2-confirm:active {
            transform: translateY(2px) !important;
            box-shadow: 2px 2px 0px #111827 !important;
        }

        /* ── Cancel button ──────────────────────────────────── */
        .swal-neo .swal2-cancel {
            font-family: 'Inter', sans-serif !important;
            font-weight: 700 !important;
            font-size: 0.875rem !important;
            border-radius: 18px !important;
            border: 4px solid #111827 !important;
            box-shadow: 4px 4px 0px #111827 !important;
            padding: 0.625rem 1.5rem !important;
            background: #FFFFFF !important;
            color: #111827 !important;
            transition: transform 150ms ease-out, box-shadow 150ms ease-out !important;
        }
        .swal-neo .swal2-cancel:hover {
            transform: translateY(-2px) !important;
            box-shadow: 6px 6px 0px #111827 !important;
        }
        .swal-neo .swal2-cancel:active {
            transform: translateY(2px) !important;
            box-shadow: 2px 2px 0px #111827 !important;
        }

        /* ── Actions area ───────────────────────────────────── */
        .swal-neo .swal2-actions {
            margin-top: 1.5rem !important;
            gap: 0.75rem !important;
        }

        /* ── No backdrop blur ───────────────────────────────── */
        .swal2-backdrop-show {
            background: rgba(17,24,39,0.55) !important;
            backdrop-filter: none !important;
        }
    </style>

    <script>
        /* ── SweetAlert2 Global Mixins ───────────────────────────────
           Pakai window.Swal_neo dan window.Swal_toast di seluruh app  */
        window.SwalNeo = Swal.mixin({
            customClass: { popup: 'swal-neo' },
            background: '#FFFFFF',
            color: '#111827',
            buttonsStyling: true,
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#FFFFFF',
        });

        window.SwalToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            customClass: { popup: 'swal-neo-toast' },
            background: '#FFFFFF',
            color: '#111827',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });

        window.SwalDanger = Swal.mixin({
            customClass: { popup: 'swal-neo' },
            background: '#FFFFFF',
            color: '#111827',
            buttonsStyling: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#FFFFFF',
            showCancelButton: true,
            cancelButtonText: 'Batal',
        });

        /* ── Livewire events ─────────────────────────────────────── */
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal:success', (data) => {
                SwalToast.fire({
                    icon: 'success',
                    title: data[0].title || 'Berhasil!',
                    text: data[0].text || '',
                });
            });
            Livewire.on('swal:error', (data) => {
                SwalToast.fire({
                    icon: 'error',
                    title: data[0].title || 'Gagal!',
                    text: data[0].text || '',
                });
            });
            Livewire.on('swal:confirm', (data) => {
                SwalDanger.fire({
                    title: data[0].title || 'Apakah kamu yakin?',
                    text: data[0].text || '',
                    icon: 'warning',
                    confirmButtonText: data[0].confirmText || 'Ya, hapus!',
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