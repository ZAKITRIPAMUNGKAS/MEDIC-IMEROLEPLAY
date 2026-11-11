# Dokumentasi API Monitoring Jam Duty Medis
**Sistem Terintegrasi: Medical Center IME × Web Farmasi × Sertifikasi Heli**

Dokumentasi ini dibuat untuk memudahkan integrasi antara website **Medical Center IME** dengan sistem eksternal, khususnya **Web Farmasi** dan tim penguji **Sertifikat Helikopter (Heli)**.

---

## 🔐 1. Keamanan & Autentikasi (API Key)

Semua permintaan ke API wajib menyertakan **API Key** yang valid. Anda dapat mengirimkannya melalui salah satu dari cara berikut:

1. **Header HTTP (Direkomendasikan):**
   ```http
   X-API-Key: farmasi_sec_4f92d8a1c70e3b6e2954
   ```
2. **Authorization Bearer:**
   ```http
   Authorization: Bearer farmasi_sec_4f92d8a1c70e3b6e2954
   ```
3. **Query Parameter URL (Paling Praktis):**
   ```http
   ?api_key=farmasi_sec_4f92d8a1c70e3b6e2954
   ```

> **Catatan:** Jika API Key salah atau tidak dikirim, server akan merespons dengan status `401 Unauthorized`.

---

## 🚀 2. Daftar Endpoint

Base URL: `https://medicalcenterime.my.id/api/duty-hours`

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/duty-hours` | Mengambil rekap jam duty staf medis dengan filter periode, nama, role, dll. |
| `GET` | `/api/duty-hours/summary` | Ringkasan cepat statistik duty & 5 staf dengan jam kerja terbanyak. |
| `GET` | `/api/duty-hours/{identifier}` | Cek detail jam duty 1 orang spesifik (`citizen_id`, `staff_id`, atau `player_id`). |

---

## 📌 3. Detail Endpoint & Parameter Filter

### A. GET `/api/duty-hours` (Daftar & Rekap Jam Duty)

#### Parameter Query (Opsional):
- **`period`**: Pilihan periode cepat:
  - `today` : Hari ini
  - `yesterday` : Kemarin
  - `1_week` : 7 hari terakhir (rolling)
  - `this_week` : Senin s/d Minggu minggu ini berjalan
  - `last_week` : Minggu lalu
  - `1_month` : 30 hari terakhir (rolling)
  - `this_month` : Bulan berjalan (*Default*)
  - `last_month` : Bulan lalu
  - `all` : Seluruh waktu
- **`date_from`** / **`start_date`**: Tanggal awal rentang kustom (`YYYY-MM-DD`).
- **`date_to`** / **`end_date`**: Tanggal akhir rentang kustom (`YYYY-MM-DD`).
- **`citizen_id`** / **`staff_id`**: Filter identitas karakter medis tertentu.
- **`search`** / **`name`**: Pencarian sebagian nama staf.
- **`hospital`**: Filter rumah sakit (`alta` / `roxwood`).
- **`min_hours`**: Hanya tampilkan staf dengan jam duty minimal X jam (contoh: `min_hours=10`).
- **`heli_threshold`**: Ambang batas jam syarat sertifikat heli (default: `10` jam).
- **`heli_eligible_only`**: `true` untuk hanya menampilkan staf yang sudah memenuhi syarat heli.
- **`is_on_duty`**: `true` (hanya yang sedang bertugas sekarang) / `false` (yang sedang off duty).
- **`duty_only`**: `true` untuk mengabaikan staf yang jam duty-nya 0 pada periode tersebut.
- **`include_sessions`**: `true` untuk menampilkan rincian tiap sesi clock-in & clock-out.
- **`sort_by`**: `total_hours` (default), `name`, `sessions`, `last_duty`.
- **`sort_dir`**: `desc` (terbanyak ke terkecil, default) / `asc`.
- **`limit`** / **`per_page`**: Jumlah data per halaman (default: `50`, gunakan `-1` untuk semua data).
- **`page`**: Nomor halaman paginasi (default: `1`).

#### Contoh Request:
```bash
# Ambil jam duty 1 bulan terakhir dengan syarat heli min 15 jam
GET /api/duty-hours?period=1_month&heli_threshold=15
Header: X-API-Key: farmasi_sec_4f92d8a1c70e3b6e2954
```

#### Contoh Response JSON:
```json
{
  "success": true,
  "period": "1_month",
  "date_range": {
    "from": "2026-10-13 00:00:00",
    "to": "2026-11-11 23:59:59"
  },
  "summary": {
    "total_medical_staff": 25,
    "active_duty_staff": 18,
    "currently_on_duty": 3,
    "heli_eligible_count": 12,
    "heli_threshold_hours": 15,
    "cumulative_hours": 420.5,
    "cumulative_minutes": 25230
  },
  "pagination": {
    "current_page": 1,
    "per_page": 50,
    "total_items": 25,
    "total_pages": 1,
    "has_next": false,
    "has_prev": false
  },
  "data": [
    {
      "user_id": 4,
      "citizen_id": "MED-012",
      "staff_id": "MED-012",
      "name": "dr. Zaki Tri Pamungkas, Sp.B",
      "hospital": "Alta Hospital",
      "role": "Dokter Spesialis",
      "role_name": "dokter_spesialis",
      "total_hours": 32.5,
      "total_minutes": 1950,
      "total_seconds": 117000,
      "formatted_duty_time": "32 Jam 30 Menit",
      "formatted_clock": "32:30:00",
      "total_sessions": 14,
      "is_currently_on_duty": true,
      "last_duty_at": "2026-11-11 20:15:00",
      "heli_eligible": true,
      "heli_threshold_hours": 15,
      "heli_hours_remaining": 0
    }
  ]
}
```

---

### B. GET `/api/duty-hours/summary` (Ringkasan Cepat)

Mengembalikan total jam seluruh staf medis beserta Top 5 staf dengan jam duty terbanyak.

#### Contoh Request:
```bash
GET /api/duty-hours/summary?period=1_week
Header: X-API-Key: farmasi_sec_4f92d8a1c70e3b6e2954
```

---

### C. GET `/api/duty-hours/{identifier}` (Pemeriksaan Personal / Sertifikat Heli)

Endpoint ini sangat cocok untuk **Verifikasi Ujian Sertifikat Heli** bagi calon penerbang medis. Masukkan `citizen_id`, `staff_id`, atau `player_id` dokter yang diuji.

#### Contoh Request:
```bash
GET /api/duty-hours/MED-012?period=1_month&heli_threshold=10
Header: X-API-Key: farmasi_sec_4f92d8a1c70e3b6e2954
```

#### Contoh Response JSON:
```json
{
  "success": true,
  "period": "1_month",
  "date_range": {
    "from": "2026-10-13 00:00:00",
    "to": "2026-11-11 23:59:59"
  },
  "data": {
    "user_id": 4,
    "citizen_id": "MED-012",
    "name": "dr. Zaki Tri Pamungkas, Sp.B",
    "hospital": "Alta Hospital",
    "role": "Dokter Spesialis",
    "total_hours": 18.25,
    "total_minutes": 1095,
    "formatted_duty_time": "18 Jam 15 Menit",
    "formatted_clock": "18:15:00",
    "total_sessions": 8,
    "is_currently_on_duty": false,
    "heli_status": {
      "eligible": true,
      "threshold_hours": 10,
      "hours_achieved": 18.25,
      "hours_remaining": 0,
      "percentage": 100
    },
    "sessions": [
      {
        "session_id": 102,
        "work_date": "2026-11-11",
        "clock_in": "2026-11-11 14:00:00",
        "clock_out": "2026-11-11 16:30:00",
        "duration_sec": 9000,
        "duration_fmt": "02:30:00",
        "is_active": false
      }
    ]
  }
}
```

---

## 💻 4. Contoh Integrasi di Sisi Web Farmasi

### Contoh Menggunakan JavaScript (Fetch):
```javascript
const API_URL = 'https://medicalcenterime.my.id/api/duty-hours';
const API_KEY = 'farmasi_sec_4f92d8a1c70e3b6e2954';

// Ambil data jam duty 1 minggu terakhir
async function fetchMedicalDuty() {
    try {
        const response = await fetch(`${API_URL}?period=1_week`, {
            method: 'GET',
            headers: {
                'X-API-Key': API_KEY,
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        if (result.success) {
            console.log("Total Staf Medis:", result.summary.total_medical_staff);
            console.log("Daftar Medis:", result.data);
        } else {
            console.error("Error:", result.message);
        }
    } catch (err) {
        console.error("Koneksi gagal:", err);
    }
}
```

### Contoh Menggunakan PHP (cURL):
```php
<?php
$apiKey = 'farmasi_sec_4f92d8a1c70e3b6e2954';
$url = 'https://medicalcenterime.my.id/api/duty-hours?period=1_month';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $apiKey,
    'Accept: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
print_r($data);
```
