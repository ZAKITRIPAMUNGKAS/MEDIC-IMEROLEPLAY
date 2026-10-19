# Panduan Integrasi Absensi FiveM ke Website EMS-IME

Dokumen ini ditujukan untuk **Teknisi/Developer FiveM** guna memperbaiki sinkronisasi status *Staff On Duty* agar terupdate secara real-time di dashboard website.

---

## 🔍 Masalah Utama saat Ini
Berdasarkan analisis log API website, script FiveM saat ini **hanya mengirimkan data (HTTP POST) saat player melakukan Clock-Out (Off-Duty)**. 
Akibatnya:
* Selama staff sedang online/on-duty di dalam game, website tidak menerima data mulai duty.
* Status *Staff On Duty* di website selalu menampilkan angka **0**.

---

## 💡 Solusi
Script FiveM harus mengirimkan request API **dua kali**:
1. **Saat Clock-In (Mulai Duty):** Kirim data dengan kolom `clock_out` bernilai `nil` / tidak diisi. Ini akan membuat status di website menjadi aktif (On-Duty).
2. **Saat Clock-Out (Selesai Duty):** Kirim data lengkap beserta waktu `clock_out` dan durasi bermain. Ini akan menutup sesi absensi di website.

---

## 💻 Script Server-Side (Lua)

Silakan gunakan atau sesuaikan potongan kode di bawah ini pada resource FiveM Anda:

### 1. Konfigurasi API (Letakkan di config/server)
```lua
local API_URL = "https://medicalcenterime.my.id/api/absensi"
local API_KEY = "ISI_DENGAN_API_KEY_DARI_ENV_WEB" -- Sesuaikan dengan API_KEY di file .env Laravel website
```

### 2. Fungsi Helper HTTP Request
```lua
local function SendAttendanceToWeb(playerId, playerName, clockInTime, clockOutTime, timeOnDuty)
    local data = {
        player_id = tostring(playerId),
        player_name = tostring(playerName),
        clock_in = clockInTime, -- Format wajib: "YYYY-MM-DD HH:MM:SS"
        clock_out = clockOutTime, -- Kirim nil jika baru Clock-In
        time_on_duty = timeOnDuty -- Kirim nil jika baru Clock-In, atau format "HH:MM:SS" jika Clock-Out
    }

    local jsonData = json.encode(data)

    PerformHttpRequest(API_URL, function(statusCode, response, headers)
        if statusCode == 200 or statusCode == 201 then
            print("[ABSENSI WEB] Berhasil sinkronisasi status ke website. HTTP Code: " .. statusCode)
        else
            print("[ABSENSI WEB] Gagal sinkronisasi. HTTP Code: " .. (statusCode or "unknown"))
            print("[ABSENSI WEB] Response: " .. (response or "no response"))
        end
    end, 'POST', jsonData, { 
        ['Content-Type'] = 'application/json',
        ['X-API-Key'] = API_KEY
    })
end
```

---

## 🛠️ Cara Implementasi di Event Handler

### A. Event Saat Player Mulai Duty (Clock-In)
Panggil fungsi ini saat mendeteksi player masuk status On-Duty di game:
```lua
local playerId = "U6ID7709" -- Isi dengan citizenid / license player
local playerName = "Zoel Lysander" -- Nama player di game
local clockInTime = os.date("%Y-%m-%d %H:%M:%S") -- Waktu mulai sekarang

-- Kirim status mulai (clock_out dan time_on_duty dikirim nil)
SendAttendanceToWeb(playerId, playerName, clockInTime, nil, nil)
```

### B. Event Saat Player Selesai Duty (Clock-Out)
Panggil fungsi ini saat player keluar status On-Duty (atau saat player disconnect):
```lua
local playerId = "U6ID7709"
local playerName = "Zoel Lysander"
local clockInTime = "2026-06-11 12:00:00" -- Waktu clock-in awal yang diambil dari session/DB game
local clockOutTime = os.date("%Y-%m-%d %H:%M:%S") -- Waktu selesai sekarang
local timeOnDuty = "01:15:00" -- Durasi on-duty (Format HH:MM:SS, opsional)

-- Kirim status selesai lengkap
SendAttendanceToWeb(playerId, playerName, clockInTime, clockOutTime, timeOnDuty)
```

---

## 🔑 Kebutuhan Data Valid pada Parameter
* **`player_id`**: Harus sama dengan `citizen_id` atau `staff_id` yang didaftarkan staff pada profil akun website mereka. Jika berbeda, sistem website akan menolak integrasi data.
* **Format Waktu**: Waktu `clock_in` dan `clock_out` harus menggunakan format string `"YYYY-MM-DD HH:MM:SS"`.
