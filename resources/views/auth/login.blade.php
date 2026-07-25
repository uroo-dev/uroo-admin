@php
    $title = 'Login';
    $layout = 'auth';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#4F46E5', 'border-dark': '#111827', 'txt-primary': '#111827', 'txt-secondary': '#6B7280', surface: '#FFFFFF', bgmain: '#F8F8F8' },
                    borderRadius: { button: '18px', input: '16px', card: '20px' },
                    boxShadow: { 'hard': '8px 8px 0px #111827', 'hard-hover': '10px 10px 0px #111827', 'hard-pressed': '4px 4px 0px #111827' },
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-bgmain text-txt-primary antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary rounded-button shadow-hard mb-4">
                <span class="text-white font-extrabold text-3xl">U</span>
            </div>
            <h1 class="text-3xl font-extrabold">UROO.DEV</h1>
            <p class="text-txt-secondary font-medium mt-1">Workspace — Masuk ke akunmu</p>
        </div>

        {{-- Card --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    @error('email')
                        <p class="text-xs font-medium text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    @error('password')
                        <p class="text-xs font-medium text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 border-4 border-border-dark rounded-sm accent-primary">
                        <span class="text-sm font-medium">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out">
                    Masuk
                </button>

                <p class="text-center text-sm text-txt-secondary">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-bold text-primary hover:underline">Daftar</a>
                </p>
            </form>
        </div>
    </div>

</body>
</html>