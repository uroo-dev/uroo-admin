# 🤖 AGENTS.md — UROO.DEV Workspace

Dokumen ini berisi standar kerja, aturan wajib, workflow, dan best practices yang harus dipatuhi oleh setiap AI Coding Agent yang bekerja pada repository ini.

---

# 1. Project Context

## Project Name

UROO.DEV Workspace

## Project Description

UROO.DEV Workspace adalah aplikasi desktop-first berbasis Laravel yang berfungsi sebagai operating system untuk software developer dan freelancer.

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

Seluruh fitur dibangun dalam satu workspace yang modular.

---

# 2. Technology Stack

Framework

- Laravel 12+

Language

- PHP 8.3+

Database

- MySQL

Frontend

- Blade
- Tailwind CSS
- Livewire
- Alpine.js

Package Manager

- Composer
- NPM

Development Environment

- Linux
- Ubuntu 24.04
- Nginx
- PHP-FPM

Version Control

- Git
- GitHub

---

# 3. Project Architecture

Gunakan Modular Architecture.

Pisahkan setiap fitur menjadi module yang independen.

Contoh:

Modules/

Dashboard/

GitHub/

Credential/

Client/

Invoice/

Notes/

Ideas/

Savings/

QualityControl/

Setiap module minimal memiliki:

- Model
- Controller
- Service
- Request
- Policy
- Livewire Component
- View
- Migration
- Seeder
- Factory
- Route

Jangan mencampurkan business logic antar module.

---

# 4. Coding Standards

Gunakan:

- PSR-12
- Laravel Best Practices
- SOLID Principle
- DRY Principle
- KISS Principle

Selalu gunakan:

- Service Class
- Form Request Validation
- Route Model Binding
- Resource Controller
- Eloquent ORM

Hindari:

- Duplicate Code
- Hardcoded Value
- Business Logic di Controller
- Query SQL mentah jika Eloquent dapat digunakan.

---

# 5. Security

WAJIB:

Gunakan:

- CSRF Protection
- Form Request Validation
- XSS Protection
- SQL Injection Prevention
- Mass Assignment Protection
- Password Hashing
- Authorization Policy
- Authentication Middleware

Data sensitif seperti:

- Password
- SSH Key
- API Key
- Database Password
- Hosting Credential

WAJIB dienkripsi sebelum disimpan.

Jangan pernah menyimpan credential di source code.

Semua konfigurasi menggunakan .env.

---

# 6. Database Rules

Gunakan:

Migration

Seeder

Factory

Relationship

Index

Foreign Key

Soft Delete jika diperlukan.

Semua tabel harus memiliki:

created_at

updated_at

Gunakan UUID hanya jika memang diperlukan.

---

# 7. Laravel Best Practices

Gunakan:

Eloquent Relationship

Resource Collection

Policy

Gate

Notification

Queue

Job

Cache

Storage

Event

Listener

Jangan menggunakan:

DB::table()

Jika masih dapat menggunakan Model.

---

# 8. Folder Rules

Jangan mengubah struktur folder utama tanpa instruksi.

Jangan menghapus file konfigurasi.

Jangan memindahkan file antar module.

Gunakan namespace yang konsisten.

---

# 9. Naming Convention

Gunakan Bahasa Inggris.

Contoh:

ClientController

InvoiceService

ProjectRepository

CredentialRequest

GitHubService

SavingsController

Gunakan:

camelCase

untuk variable.

PascalCase

untuk Class.

snake_case

untuk database.

kebab-case

untuk route.

---

# 10. Performance

Gunakan:

Lazy Loading hanya jika diperlukan.

Gunakan:

Eager Loading

untuk Relationship.

Hindari:

N+1 Query

Gunakan Pagination.

Optimalkan Query.

Gunakan Cache jika memungkinkan.

---

# 11. UI Development

Seluruh aturan UI berada pada:

design.md

AI Agent TIDAK BOLEH membuat desain sendiri.

Seluruh warna,

spacing,

komponen,

animasi,

layout,

typography,

shadow,

radius,

mengikuti design.md.

---

# 12. Documentation

Setiap fitur baru harus memiliki:

Komentar seperlunya.

Penamaan yang jelas.

Kode mudah dipahami.

Jika membuat module baru:

Update README jika diperlukan.

---

# 13. Git Workflow (WAJIB)

Untuk setiap fitur kecil:

1.

Implement Feature

↓

2.

Self Review

↓

3.

Fix Warning

↓

4.

Syntax Check

↓

5.

Commit

↓

6.

Push

Commit Message Convention

feat:

fix:

refactor:

style:

docs:

test:

chore:

Contoh:

feat: add credential vault

fix: invoice total calculation

refactor: optimize github service

---

# 14. Auto Push

Setelah:

✅ Tidak ada syntax error

✅ Tidak ada fatal error

✅ Fitur selesai

WAJIB menjalankan:

git add .

git commit -m "feat: ..."

git push origin main

Push akan menjadi trigger deployment CI/CD.

---

# 15. Validation Before Finish

Sebelum menyelesaikan pekerjaan WAJIB memastikan:

✔ Tidak ada syntax error

✔ Tidak ada import yang tidak digunakan

✔ Tidak ada duplicate code

✔ Tidak ada dead code

✔ Tidak ada variable yang tidak digunakan

✔ Tidak ada migration conflict

✔ Tidak ada route conflict

✔ Tidak ada merge conflict

✔ Semua feature berjalan

---

# 16. Restrictions

DILARANG:

❌ Push ketika project error

❌ Menghapus database

❌ DROP DATABASE

❌ rm -rf

❌ Mengubah struktur utama project

❌ Menghapus migration lama

❌ Menghapus data production

❌ Hardcode credential

❌ Menyimpan API Key di source code

❌ Menonaktifkan middleware tanpa izin

❌ Menghapus policy

---

# 17. AI Behaviour

AI harus:

- Berpikir sebelum mengubah kode.
- Mengutamakan keamanan.
- Mengutamakan maintainability.
- Mengutamakan readability.
- Mengutamakan modularity.
- Mengutamakan scalability.
- Menjaga konsistensi dengan codebase yang sudah ada.
- Tidak membuat perubahan di luar ruang lingkup tugas yang diminta.
- Menggunakan kembali komponen, service, dan helper yang sudah ada sebelum membuat implementasi baru.

Jika terdapat konflik antara permintaan pengguna, design.md, dan AGENTS.md:

1. Ikuti instruksi pengguna.
2. Jika tidak bertentangan, ikuti AGENTS.md.
3. Seluruh aturan tampilan dan UX wajib mengikuti design.md.
