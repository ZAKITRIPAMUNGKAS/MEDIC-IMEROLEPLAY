# EMS-IME — Sistem Manajemen Staf Medis

Sistem manajemen staf rumah sakit berbasis web untuk komunitas **IME Medical Center** (Emergency Medical Services, server roleplay FiveM). Aplikasi ini menangani absensi real-time yang tersinkron dengan server game, penggajian mingguan otomatis, formulir medis publik, live chat, hingga rekap tahunan "Wrapped" untuk setiap staf.

**Live:** [medicalcenterime.my.id](https://medicalcenterime.my.id)

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?logo=livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white)

---

## Fitur Utama

**Absensi & duty tracking**
- Clock in/out real-time dengan banyak sesi per hari dan duty timer (auto checkout saat timer habis)
- Sesi yang melewati tengah malam atau pergantian minggu dipecah otomatis
- Sinkronisasi status on-duty dari server FiveM melalui REST API (`POST /api/absensi`, dilindungi API key)
- Force checkout, input manual, serta edit/hapus absensi oleh admin

**Penggajian**
- Generate payroll mingguan otomatis berdasarkan total jam kerja × gaji per role
- Override gaji per staf, status Pending / Paid / Cancelled, deteksi duplikat, dan export Excel/CSV
- Notifikasi gaji ke setiap staf

**Manajemen staf & hak akses**
- Hierarki role (Trainee → Perawat → Co-Ass → Dokter Umum → Dokter Spesialis → Admin)
- Sistem permission per role dan per user, aktivasi akun manual oleh admin
- Dukungan multi-rumah sakit (Alta & Roxwood)

**Layanan publik**
- Formulir medis tanpa login: surat kesehatan, tes psikologi, surat psikolog, operasi plastik, pendaftaran karakter
- Alur review dan approval oleh staf, dengan notifikasi Telegram
- Live chat pasien–staf (Livewire), feedback, dan testimoni

**Portal departemen**
- Rekrutmen & interview, training, promosi, pengajuan cuti, dan resign
- Pengajuan dan penerbitan sertifikasi (operasi, kendaraan, dan lainnya) serta kontrak staf

**Lainnya**
- Leaderboard dan dashboard staf
- **Wrapped**: rekap tahunan aktivitas tiap staf
- Halaman struktur organisasi rumah sakit

## Tech Stack

| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 12, PHP 8.2+, Eloquent ORM |
| Frontend | Blade, Livewire 3, Alpine.js, Tailwind CSS |
| Database | MySQL / MariaDB (produksi), SQLite (development) |
| Integrasi | REST API untuk server FiveM (Lua), Telegram Bot SDK |
| Infrastruktur | Database queue, cache & session; cron untuk sesi kedaluwarsa; deploy di cPanel |

## Arsitektur Singkat

```
FiveM server (Lua) ──POST /api/absensi──▶ Laravel API ──▶ attendances
                                                    │
Staf / Admin ──▶ Dashboard (Blade + Livewire) ──────┼──▶ payrolls, users, roles
                                                    │
Publik ──▶ Formulir medis & live chat ──────────────┴──▶ Telegram notification
```

- `app/Http/Controllers/` — dipisah per area: `Admin/`, `Staff/`, `Api/`, `Portal/`, `Public/`
- `app/Livewire/` — komponen real-time (live chat, feedback, pesan anggota)
- `app/Console/Commands/` — job terjadwal, mis. checkout otomatis sesi kedaluwarsa
- `database/migrations/` — skema lengkap (users, staff_roles, attendances, payrolls, medical_forms, dll.)
- `tests/` — feature & unit test untuk alur absensi dan clock in/out
- `scripts/fivem_absensi_script.lua` — contoh script sisi server FiveM

## Menjalankan Secara Lokal

```bash
git clone https://github.com/ZAKITRIPAMUNGKAS/MEDIC-IMEROLEPLAY.git
cd MEDIC-IMEROLEPLAY

composer install
npm install

cp .env.example .env
php artisan key:generate

# atur koneksi database di .env, lalu:
php artisan migrate --seed
npm run build

php artisan serve
```

Menjalankan test:

```bash
php artisan test
```

## Dokumentasi

- [Dokumentasi lengkap](docs/DOCUMENTATION.md): fitur, skema database, daftar route, deployment, troubleshooting
- [Panduan integrasi FiveM](docs/fivem-integration-guide.md): cara server game mengirim data clock in/out ke website

## Developer

Dibangun dan dikelola oleh **Zaki Tri Pamungkas**
[GitHub](https://github.com/ZAKITRIPAMUNGKAS) · [LinkedIn](https://linkedin.com/in/zaki-tri-pamungkas-059830255)
