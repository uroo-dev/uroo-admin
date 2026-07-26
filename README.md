# UROO.DEV Workspace

Aplikasi desktop-first berbasis Laravel yang berfungsi sebagai operating system untuk software developer dan freelancer.

Aplikasi ini menggabungkan:

- Project Management
- GitHub Monitor
- Credential Vault
- Client Management
- Invoice Generator
- Developer Notes
- App Idea Vault
- Savings Manager
- Quality Control Checklist

## Teknologi

- **Framework**: Laravel 12+
- **Language**: PHP 8.3+
- **Database**: MySQL
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Package Manager**: Composer, NPM

## Instalasi

```bash
git clone https://github.com/uroo-dev/uroo-admin.git
cd uroo-admin
composer install
cp .env.example .env
php artisan key:generate

# Edit .env sesuaikan database dan GitHub token
php artisan migrate --seed
php artisan serve
```

## Konfigurasi GitHub Monitor

Tambahkan token GitHub personal access permission di `.env`:

```
GITHUB_TOKEN=ghp_your_personal_access_token
GITHUB_USERNAME=github_username
```

Repository akan otomatis tersinkronisasi saat halaman GitHub Monitor dikunjungi pertama kali. Tidak perlu tombol sync manual.

## Halaman Utama

| Route | Halaman |
|-------|---------|
| `/` | Dashboard |
| `/github` | GitHub Monitor |
| `/projects` | Projects |
| `/clients` | Clients |
| `/credentials` | Credential Vault |
| `/invoices` | Invoices |
| `/notes` | Developer Notes |
| `/ideas` | App Ideas |
| `/bookmarks` | Bookmarks |
| `/brain-dumps` | Brain Dump |
| `/savings` | Savings Vault |
| `/subscriptions` | Subscriptions |
| `/quality-control` | Quality Control |

## Struktur Folder

```
app/
├── Http/
│   ├── Controllers/
│   ├── Livewire/
│   └── Requests/
├── Models/
├── Services/
resources/
├── css/
├── js/
└── views/
    ├── layouts/
    ├── github/
    ├── livewire/
    └── [feature views]/
```

## Lisensi

Private.