# 🎨 design.md — UROO.DEV Workspace (Neo Brutalism Design System)

Dokumen ini merupakan **Design System** yang wajib diikuti oleh seluruh AI Coding Agent saat membangun antarmuka aplikasi.

Seluruh halaman, komponen, warna, tipografi, animasi, layout, dan UX harus mengikuti aturan pada dokumen ini.

AI **TIDAK BOLEH** membuat style baru di luar design.md tanpa instruksi pengguna.

---

# 1. Design Philosophy

Design Style

**Neo Brutalism**

Inspirasi:

- Gumroad
- Figma Community
- Mono
- Pitch
- Linear (layout)
- Notion (spacing)
- Retro Operating System
- Windows 95 (playful interaction)
- Modern Startup Dashboard

Karakter utama:

- Bold
- Playful
- Highly Readable
- Strong Contrast
- Thick Borders
- Flat Colors
- Large Typography
- Fun Interaction
- Minimal Blur
- No Glassmorphism
- No Neumorphism
- No Claymorphism

Interface harus terasa seperti software modern dengan karakter unik dan percaya diri.

---

# 2. Visual Rules

WAJIB menggunakan:

✅ Border hitam tebal

✅ Shadow kotak (hard shadow)

✅ Flat color

✅ Sudut sedikit membulat

✅ Kontras tinggi

✅ Icon besar

✅ Typography tegas

DILARANG menggunakan:

❌ Blur

❌ Frosted Glass

❌ Soft Shadow

❌ Gradient berlebihan

❌ Transparansi tinggi

❌ Glow

❌ Inner Shadow

---

# 3. Color Palette

## Background

#F8F8F8

---

## Surface

#FFFFFF

---

## Primary

#4F46E5

---

## Secondary

#3B82F6

---

## Success

#22C55E

---

## Warning

#F59E0B

---

## Danger

#EF4444

---

## Pink Accent

#FF66C4

---

## Yellow Accent

#FFD93D

---

## Cyan Accent

#67E8F9

---

## Purple Accent

#A855F7

---

## Black Border

#111827

---

## Primary Text

#111827

---

## Secondary Text

#6B7280

---

# 4. Border Rules

Semua komponen wajib memiliki border.

Border Width

4px

Border Color

#111827

Tidak boleh menggunakan border tipis.

---

# 5. Shadow Rules

Gunakan Hard Shadow.

Contoh

box-shadow:

8px 8px 0px #111827

Hover

10px 10px 0px #111827

Pressed

4px 4px 0px #111827

Tidak boleh menggunakan blur shadow.

---

# 6. Border Radius

Card

20px

Button

18px

Input

16px

Modal

24px

Avatar

50%

---

# 7. Typography

Gunakan:

Inter

atau

Manrope

Fallback

sans-serif

---

Heading

Bold

700–800

---

Body

Medium

500

---

Caption

Regular

400

---

Ukuran

H1

36px

H2

30px

H3

24px

Body

16px

Small

14px

---

# 8. Layout

Desktop First

Sidebar kiri

Top Navbar

Scrollable Content

Max Width

1440px

Spacing System

8px

Gunakan kelipatan:

8

16

24

32

40

48

64

---

# 9. Sidebar

Lebar

280px

Background

White

Border kanan

4px hitam

Logo besar

Menu besar

Icon besar

Menu aktif

Background Primary

Text putih

Shadow keras

Hover

Sedikit naik

Cursor pointer

---

# 10. Navbar

Sticky

Height

80px

Search Box

Notification

Theme Switch

Profile

Border bawah

4px

---

# 11. Cards

Semua card wajib memiliki:

Background putih

Border hitam 4px

Hard Shadow

Radius 20px

Padding besar

Hover

Translate

-6px

Shadow bertambah

---

# 12. Buttons

Gunakan button besar.

Primary

Background

Primary

Text putih

Border hitam

Hover

Naik

Pressed

Turun

Shadow mengecil

Danger

Merah

Success

Hijau

Warning

Kuning

Secondary

Putih

---

# 13. Input

Flat

Background putih

Border hitam

Radius 16px

Padding besar

Focus

Border Primary

Tidak memakai glow.

---

# 14. Tables

Border tebal.

Header

Primary

Text putih

Row Hover

Background abu terang.

Action Button

Icon besar.

---

# 15. Modal

Background putih.

Border hitam.

Shadow keras.

Scale animation.

---

# 16. Badges

Status menggunakan warna.

Development

Blue

Testing

Yellow

Revision

Orange

Completed

Green

Archived

Gray

Production

Purple

---

# 17. Charts

Gunakan:

Bar Chart

Line Chart

Pie Chart

Area Chart

Style

Flat

Bold

Tanpa gradient.

---

# 18. Icons

Gunakan:

Heroicons

atau

Lucide

Ukuran

22px

24px

28px

Jangan gunakan icon tipis.

---

# 19. Animation

Gunakan animasi sederhana.

Hover Card

translateY(-6px)

Hover Button

translateY(-3px)

Click

translateY(4px)

Modal

Scale

Fade

Sidebar

Slide

Toast

Slide Right

Dropdown

Scale

Durasi

200ms

Easing

ease-out

Tidak menggunakan animasi berlebihan.

---

# 20. Dashboard Rules

Stat Card

Besar

Ikon kiri

Value besar

Label kecil

Chart

Lebar

Quick Action

Grid

Notification

Timeline

Recent Activity

Card

---

# 21. Empty State

Gunakan ilustrasi sederhana.

Judul besar.

Penjelasan singkat.

Button CTA.

---

# 22. Loading

Skeleton Loading.

Spinner sederhana.

Progress Bar.

---

# 23. Responsive

Desktop

≥1280px

Laptop

1024px

Tablet

768px

Mobile

480px

Sidebar menjadi drawer pada tablet dan mobile.

---

# 24. Accessibility

Kontras minimum WCAG AA.

Focus State jelas.

Ukuran klik minimum

44x44px.

Keyboard Navigation wajib berfungsi.

---

# 25. Component Rules

Semua komponen wajib konsisten.

Tidak boleh:

- warna berbeda
- radius berbeda
- shadow berbeda
- border berbeda
- typography berbeda

Gunakan komponen yang sudah ada sebelum membuat komponen baru.

---

# 26. UX Rules

Prioritaskan:

- Kecepatan
- Konsistensi
- Readability
- Hierarki visual
- Sedikit klik
- Informasi mudah ditemukan

---

# 27. Design Principles

Setiap halaman harus terasa seperti aplikasi SaaS premium dengan gaya Neo Brutalism.

Checklist:

✅ Bold Typography

✅ Thick Borders

✅ Hard Shadows

✅ Flat Colors

✅ Large Components

✅ High Contrast

✅ Playful Interaction

✅ Consistent Spacing

✅ Reusable Components

✅ Desktop-first

Jangan mengubah design system tanpa instruksi langsung dari pengguna.
