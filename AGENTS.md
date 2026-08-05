# AGENTS.md — UROO.DEV Workspace

Panduan kerja untuk AI coding agent. Dokumen ini menggantikan `AGENTS.MD` lama yang berisi klaim usang.

## Stack yang benar

- Laravel 13 (`laravel/framework: ^13.8`), PHP 8.3, PHPUnit 12
- Frontend: Blade + Tailwind v4 (konfigurasi via CSS di `resources/css/app.css`, TIDAK ada `tailwind.config.js`) + Alpine.js + Vite
- **Livewire sudah dihapus** — jangan gunakan; tidak ada komponen Livewire di mana pun
- **Tidak ada direktori `Modules/`** walau `Modules\` ada di autoload composer — pakai struktur Laravel standar (`app/Models`, `app/Services`, `app/Policies`, `app/Http/Requests`)
- DB: MySQL untuk dev (di `.env`), SQLite `:memory:` untuk test (di `phpunit.xml`); `.env.example` default sqlite
- Auth kustom (`AuthController`, view `resources/views/auth`), bukan Breeze/Jetstream
- `.npmrc` berisi `ignore-scripts=true` — postinstall npm tidak berjalan

## Command

- `composer dev` — jalankan sekaligus: `php artisan serve` + `queue:listen` + `pail` + `vite` (via concurrently)
- `composer test` — `config:clear` lalu `php artisan test` (PHPUnit, DB sqlite memory). Test tunggal: `php artisan test --filter=NamaTest`
- `composer setup` — install + salin `.env` + `key:generate` + `migrate --force` + npm install + build
- `npm run build` / `npm run dev` — build Vite (Tailwind v4)
- `php artisan pint` — format kode (PSR-12)
- `php artisan github:sync {user_id?}` — sinkronisasi repositori & commit dari GitHub

## Arsitektur & konvensi kode

- Pola route (lihat `routes/web.php`): `Route::resource(...)->only(['index','store','update','destroy'])`. `index` me-render Blade view; `store/update/destroy` pakai FormRequest + `$this->authorize(...)` lalu redirect ke index. Aksi ekstra dibuat route PATCH/POST terpisah (contoh: `notes.toggle-pin`, `savings.deposit`, `invoices.pdf`).
- Business logic di `app/Services/*Service.php`, otorisasi di `app/Policies/*Policy.php`, validasi di `app/Http/Requests`.
- Semua route fitur dibungkus `Route::middleware('auth')`; login/register pakai middleware `guest`.
- Password credential dienkripsi via accessor `Crypt::encryptString` di Model (kolom `password_encrypted`) — jangan simpan plaintext.
- GitHub Monitor butuh `GITHUB_TOKEN` + `GITHUB_USERNAME` di `.env` (dibaca lewat `config/github.php`).
- Invoice PDF via `barryvdh/laravel-dompdf`; "send WA" hanya membuka link `wa.me` (tanpa API key).

## Desain (WAJIB)

- Semua aturan UI ada di `DESIGN.MD` (Neo Brutalism). **Jangan membuat desain/style sendiri.** Border hitam 4px, hard shadow `8px 8px 0 #111827`, radius card 20px, dan seterusnya.
- Prioritas konflik: instruksi pengguna > AGENTS.md > DESIGN.MD (aturan UI tetap mengikuti DESIGN.MD).

## Penamaan & style

- Bahasa Inggris untuk kode & penamaan: `camelCase` (variabel), `PascalCase` (class), `snake_case` (DB/kolom), `kebab-case` (route). Contoh: `ClientController`, `ClientService`, `ClientRequest`.
- Setiap tabel wajib punya `created_at`/`updated_at`; perubahan skema via migration (jangan hapus migration lama).

## Git workflow (konvensi tim)

- Commit convention: `feat:`, `fix:`, `refactor:`, `style:`, `docs:`, `test:`, `chore:`
- Push ke `main` memicu deploy CI/CD — pastikan bebas error sebelum push.
- DILARANG: drop/delete database, `rm -rf`, mengubah struktur folder utama, menghapus migration lama, hardcode credential, menonaktifkan middleware tanpa izin.
