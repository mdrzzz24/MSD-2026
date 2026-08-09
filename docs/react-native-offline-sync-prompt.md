# Prompt Fitur: Offline Scan + Auto-Sync (React Native)

> **Tambahan untuk prompt app React Native (config-driven).** Fitur ini membuat
> app bisa **scan saat offline**, menyimpan data QR + waktu scan secara lokal,
> lalu **meng-upload batch** ke server begitu koneksi internet kembali.
>
> Copy-paste bagian "## Prompt Fitur (salin dari sini)" ke AI coding assistant
> untuk menambahkan fitur ini ke project yang sudah ada.

---

## Prompt Fitur (salin dari sini)

Tambahkan fitur **Offline Mode + Auto-Sync** ke aplikasi React Native (Expo)
yang sudah ada. Tujuannya: saat perangkat **tidak punya internet**, staf tetap
bisa scan QR peserta; setiap hasil scan (QR + waktu scan) **disimpan lokal dulu**,
lalu **di-upload ke server secara otomatis** begitu internet kembali.

### 1. Perilaku yang diinginkan

1. **Scan offline** — setiap aksi scan (registrasi, agenda/workshop, booth,
   register workshop) **selalu berhasil** disimpan di perangkat, walau sedang
   offline. Tidak ada blokir "tidak ada koneksi".
2. **Antrian lokal (queue)** — tiap scan masuk ke antrian dengan:
   - `client_id` (UUID unik, dibuat sekali per scan)
   - `action` (`registration_scan` | `agenda_scan` | `booth_scan` | `workshop_register`)
   - `qr_token` (hasil scan)
   - `item_id` (id agenda/booth/workshop sesuai aksi; `null` untuk registrasi)
   - `scanned_at` (timestamp saat scan terjadi — dipertahankan, bukan waktu upload)
   - status (`pending` | `syncing` | `synced` | `failed`)
3. **Deteksi koneksi** — pantau status internet (`@react-native-community/netinfo`).
   Saat online, mulai **sinkronisasi otomatis** antrian.
4. **Sinkronisasi batch** — kirim antrian pending ke endpoint:
   ```
   POST {base}/sync/scans
   Body: { "scans": [ { "client_id", "action", "qr_token", "item_id", "scanned_at" } ] }
   ```
   Kirim dalam batch (mis. maks 50 per request). Proses satu per satu; tiap item
   yang berhasil (`success: true`) ditandai `synced` dan dihapus dari antrian.
5. **Menangani hasil per item** — response tiap item berisi `client_id`, jadi
   app mencocokkan hasil ke antrian lokal:
   - `success: true` → tandai `synced`, simpan ringkasan (nama peserta, status).
   - `success: false` → tandai `failed`, tampilkan pesan dari `message` (mis.
     "Registrant is not registered for this workshop.") di layar "Riwayat/Sync".
   - Jangan blokir item lain — satu gagal tidak menggagalkan batch.
6. **Retry otomatis** — item `failed` karena masalah sementara (timeout/5xx)
   tetap dicoba ulang pada sinkronisasi berikutnya; item yang gagal karena
   validasi (404/403) ditandai permanen `failed` dan tidak dicoba ulang terus.
7. **Indikator UI** — tampilkan badge/ikon jumlah antrian pending (mis. "12
   scan menunggu sinkronisasi") dan status sinkronisasi (sedang sync / sukses /
   ada yang gagal). Bisa juga tombol "Sync Sekarang" manual.

### 2. Kontrak API sync (sudah tersedia di backend)

```
POST {base}/sync/scans
Content-Type: application/json

{
  "scans": [
    {
      "client_id": "a1b2c3-...",
      "action": "agenda_scan",            // registration_scan | agenda_scan | booth_scan | workshop_register
      "qr_token": "8dba725e73fd0665",
      "item_id": 94,                       // id agenda/booth/workshop; null utk registration_scan
      "scanned_at": "2026-08-09T10:00:00+07:00"
    }
  ]
}
```

Response `200` (selalu `success: true` untuk batch; hasil per item di `data`):
```json
{
  "success": true,
  "data": [
    {
      "client_id": "a1b2c3-...",
      "action": "agenda_scan",
      "success": true,
      "message": "Check-in recorded.",
      "already_visited": false,
      "registration": "approved",
      "registrant": { "id": 16, "name": "Dwiky Restu Nugroho", "email": "...", "company": "...", "job_title": "Supervisor" },
      "visited_at": "2026-08-09T10:00:00.000000Z"
    },
    {
      "client_id": "x9y8z7-...",
      "action": "agenda_scan",
      "success": false,
      "message": "Registrant is not registered for this workshop.",
      "registration": "not_registered"
    }
  ]
}
```

Catatan penting untuk app:
- **Idempoten** — mengirim scan yang sama dua kali tidak menggandakan data;
  response kedua berisi `already_visited` / `already_checked_in` /
  `already_registered = true`. Aman.
- `action` yang didukung: `registration_scan`, `agenda_scan`, `booth_scan`,
  `workshop_register`.
- `registration_scan` → set `checked_in_at` (badge MQTT **tidak** di-trigger saat
  sync offline; hanya mencatat kehadiran).
- `agenda_scan` pada **workshop** → hanya sukses jika registrasi sudah
  `approved`; kalau `not_registered`/`pending` → `success: false` (pesan jelas).
- `workshop_register` → mendaftarkan & langsung **approve** peserta ke workshop.

### 3. Implementasi yang diharapkan

- **Storage antrian**: `@react-native-async-storage/async-storage` (atau SQLite
  via `expo-sqlite` jika antrian bisa besar). Beri field status per item.
- **Koneksi**: `@react-native-community/netinfo` untuk memantau online/offline.
- **Logika sync**: service/module terpisah (mis. `src/services/SyncService.ts`)
  yang:
  - mendengar perubahan koneksi,
  - mengirim batch antrian `pending`,
  - memperbarui status per `client_id`,
  - berhenti saat offline, lanjut lagi saat online.
- **UI**: badge antrian, layar "Riwayat Sinkronisasi" (daftar scan offline +
  status), tombol "Sync Sekarang".
- Jangan merusak alur scan online yang sudah ada — online tetap panggil endpoint
  langsung (dan boleh juga masuk antrian sebagai cadangan). Paling sederhana:
  **semua scan selalu masuk antrian**, lalu sync langsung saat online (latency
  kecil). Ini membuat alur offline & online seragam dan lebih mudah dirawat.

### 4. Catatan teknis

- Pastikan timestamp `scanned_at` dikirim dalam ISO 8601 (contoh
  `2026-08-09T10:00:00+07:00`) — server akan memakai waktu ini sebagai waktu
  check-in/visit, bukan waktu upload.
- `client_id` harus unik & persisten — jangan regenerasi saat retry.
- Batasi ukuran batch (50 item) untuk menghindari payload besar.
- Tangani error jaringan (timeout, DNS) dengan retry, dan jangan sampai app
  crash saat offline.
