# Prompt: Aplikasi React Native — Login, Tracking, & Scanning (MSD26)

> Copy-paste prompt di bawah ini ke AI coding assistant (Cursor / Copilot / ChatGPT)
> untuk membangun aplikasi. Bagian dalam `###` adalah spesifikasi teknis yang bisa
> kamu ubah sesuai kebutuhan.

---

## Prompt (salin dari sini)

Buatkan sebuah **aplikasi mobile React Native (Expo)** untuk staf acara (admin
event) bernama **MSD26 Checker**. Aplikasi dipakai di handphone untuk:
1. **Login** admin,
2. **Tracking** sesi/booth (melihat agenda & booth yang aktif),
3. **Scanning** QR peserta (inisiasi utama) — check-in registrasi, kehadiran
   sesi/booth, dan registrasi workshop.

Aplikasi **hanya dikembangkan untuk Android** saat ini.

### 1. Kontrak API (wajib diikuti)

Base URL default: `http://127.0.0.1:8000/api`
**Base URL harus bisa diubah dari dalam aplikasi** (halaman "Pengaturan Server"),
disimpan di `AsyncStorage`/`SecureStore`. Semua request memakai base URL ini.
Envelope response selalu: `{ "success": bool, "message": string, "data": ... }`.

Endpoint yang dipakai:

**A. Login**
```
POST {base}/login
Body (JSON): { "email": "admin@example.com", "password": "..." }
200 → { "success": true, "message": "Login successful.", "data": { "user": {
        "id": 1, "name": "Admin Rozan", "email": "...", "is_admin": true, "role": "super_admin" } } }
401 → { "success": false, "message": "Invalid credentials." }
```
- Validasi hanya dari tabel `users` (bukan registrant). Tidak ada token — cukup
  simpan objek `user` lokal sebagai sesi.
- **WAJIB simpan `user.id` dari response login** dan kirim sebagai `admin_id`
  pada setiap request berikutnya (agenda, scan, trackout, attendee, activity,
  config). Untuk **akun ruangan** (role `room`), server memakai `admin_id` untuk
  membatasi: hanya sesi yang sudah di-assign super admin yang ditampilkan/di-track;
  kalau akun tidak di-assign → semua sesi ditampilkan.

**B. Daftar agenda (untuk tracking)**
```
GET {base}/agenda?admin_id={userId}
200 → { "success": true, "room": { "id": 4, "name": "Sumatra" } | null,
        "rooms": ["Sumatra", ...],
        "scope": { "type": "all"|"assigned"|"unrestricted", "agenda_item_ids": [...] }, "data": [ {
  "id": 94, "title": "...", "topic_headline": null, "description": "<p>...</p>",
  "agenda_type": "workshop" | "track" | "session",
  "workshop_id": 21 | null, "track_id": null | 54,
  "company": "Cloudflare" | null, "workshop_name": "Cloudflare" | null,
  "track_name": null | "Anaplan",
  "room": "Sulawesi" | null, "date": null | "2026-08-08",
  "start_time": "10:30:00", "end_time": "12:00:00",
  "capacity": 0, "is_registrable": true, "feedback_enabled": false,
  "speakers": [ { "id": 8, "name": "Ihsan Fuadi", "title": "Solutions Engineer", "photo": "http://.../storage/...png" } ]
} ] }
```
- `{userId}` = id user dari response login (untuk akun ruangan, daftar hanya
  berisi sesi yang ditugaskan super admin; akun tanpa penugasan → semua sesi).
- `room` — ruangan yang diikat ke akun ruangan (`{id, name}`); `null` untuk
  non-akun-ruangan. Bisa dibaca langsung dari `/agenda` (tanpa `/config`).
- `rooms` — daftar nama ruangan unik dari sesi yang bisa di-manage akun tsb
  (ruangan yang muncul di `data`).
- `scope` menjelaskan pembatasan: `all` (akun ruangan tanpa penugasan),
  `assigned` (hanya `agenda_item_ids`), `unrestricted` (bukan akun ruangan).

**C. Daftar booth**
```
GET {base}/booths
200 → { "success": true, "data": [ { "id": 1, "name": "AWS", "description": "...",
        "is_active": true, "order": 1, "visitor_count": 12, "created_at": "..." } ] }
```

**D. Scan check-in registrasi (print badge + check-in)**
```
POST {base}/registration/scan
Body: { "qr_token": "8dba725e73fd0665" }
200 → { "success": true, "message": "...", "already_checked_in": false,
        "printed": true, "mqtt_enabled": true, "data": { "registrant": {...}, "checked_in_at": "..." } }
404 → { "success": false, "message": "Invalid QR code. Registrant not found." }
403 → { "success": false, "message": "Registrant is not approved." }
```

**E. Scan kehadiran sesi/workshop (agenda)**
```
POST {base}/agenda/{agenda_item_id}/scan
Body: { "qr_token": "...", "admin_id": {userId} }
200 (hadir) → { "success": true, "message": "Check-in recorded.", "already_visited": false,
                "registration": "approved", "data": { "registrant": {...}, "visited_at": "..." } }
403 (belum terdaftar workshop) → { "success": false,
                "message": "Registrant is not registered for this workshop.", "registration": "not_registered" }
403 (bukan sesi akun ruangan tsb) → { "success": false,
                "message": "This session is not assigned to your account.", "scope": "forbidden" }
403 (pending) → { "success": false,
                "message": "Registrant is not yet approved for this workshop.", "registration": "pending" }
```
- Untuk sesi `workshop`: hanya dicatat jika `registration === "approved"`; kalau
  `not_registered`/`pending`, tampilkan peringatan dan JANGAN dihitung hadir.
- Untuk sesi `track`/`session`: walk-in (`registration: "not_applicable"`).

**F. Scan booth**
```
POST {base}/booths/{booth_id}/scan
Body: { "qr_token": "..." }
200 → { "success": true, "already_visited": false, "data": { "registrant": {...}, "visited_at": "..." } }
200 already → { "success": true, "already_visited": true, ... }
```

**G. Registrasi workshop via API (langsung approved)**
```
POST {base}/workshops/{workshop_id}/register
Body: { "qr_token": "..." }  ATAU  { "registrant_id": 445 }
200 → { "success": true, "already_registered": false, "data": { "workshop": {...}, "registrant": {...}, "status": "approved" } }
```

> Catatan: `workshop_id` untuk register diambil dari field `workshop_id` pada
> response `/agenda` (bukan `id` agenda).

### 2. Fitur aplikasi

1. **Splash & Konfigurasi Server**
   - Pertama kali buka: tampilkan halaman input Base URL + tombol "Tes Koneksi"
     (panggil `GET {base}/agenda` atau `/booths`).
   - Simpan base URL; ada opsi ubah lagi dari halaman Settings.

2. **Login**
   - Form email + password, tombol login.
   - Sukses → simpan `user` lokal, arahkan ke layar utama.
   - Error 401 → tampilkan pesan "Email atau password salah".
   - Ada tombol "Logout" (hapus sesi lokal).

3. **Beranda (Tracking)**
   - Tab/daftar: **Agenda** dan **Booth**.
   - Agenda: tampilkan grup per `agenda_type` (workshop/track/session) dengan
     `company`, judul, `room`, `start_time`–`end_time`, dan nama speaker.
     `description` berisi HTML — render sebagai teks biasa (strip tag).
   - Booth: daftar booth aktif dengan `visitor_count`.
   - Tarik-untuk-refresh pada kedua daftar.

4. **Scanning (fitur utama)**
   - Satu layar scanner QR (pakai `expo-camera`) yang membaca `qr_token`.
   - Setelah hasil scan, tampilkan pilihan aksi **sesuai konteks**:
     - Dari menu Registrasi → `POST /registration/scan` (badge + check-in).
     - Dari detail agenda → `POST /agenda/{id}/scan`.
     - Dari detail booth → `POST /booths/{id}/scan`.
     - Dari detail workshop → tombol "Daftarkan Peserta" → `POST /workshops/{id}/register`.
   - Tampilkan hasil scan sebagai kartu: Nama, Email, Company, Job Title, status
     sukses/peringatan, dan tombol "Scan Berikutnya".
   - Handle semua kode HTTP (200/401/403/404) dengan pesan yang jelas.

5. **Riwayat / Tracking lokal**
   - Simpan log scan terakhir secara lokal (timestamp, aksi, nama peserta,
     status) supaya bisa dilihat offline.

### 3. Stack & struktur (wajib)

- **React Native + Expo** (`npx create-expo-app`), TypeScript.
- Navigasi: `@react-navigation/native` + bottom tabs & stack.
- Networking: `axios` dengan instance yang base URL-nya dibaca dari
  `AsyncStorage` saat request (bukan hardcode).
- Model data: `interface`/`type` TypeScript untuk `ApiResponse`, `User`,
  `AgendaItem`, `Speaker`, `Booth`, `Registrant`, `ScanResult`. Semua field
  opsional (`?`) supaya tidak crash kalau response berubah.
- Storage sesi: `@react-native-async-storage/async-storage`.
- Scanner: `expo-camera` (barcode scanning), fallback input manual kode jika
  kamera tidak tersedia.

### 4. Kualitas

- State management sederhana (React Context untuk base URL, sesi user, dan log).
- Loading spinner & error state di setiap fetch.
- UI bersih, komponen reusable (Button, Card, Field, Modal hasil scan).
- Dilarang hardcode base URL di file selain satu tempat konfigurasi.
- Siapkan `README.md` cara menjalankan (install, env, ganti base URL).

---

## Catatan tambahan untuk kamu

- **Expo** paling cepat untuk development; bisa pakai `npx expo run:android`
  kalau butuh native (camera).
- Untuk tes di emulator Android, `localhost` host-nya `10.0.2.2` (bukan
  `127.0.0.1`). Contoh base URL emulator: `http://10.0.2.2:8000/api`.
- Untuk tes di handphone fisik, pakai IP LAN komputer, misal
  `http://192.168.1.10:8000/api`, dan pastikan `php artisan serve --host=0.0.0.0`.
- QR token contoh yang valid di DB: `8dba725e73fd0665`, `0044b2e37ad75ec7`.
- Login contoh (users): `rozan.chaidir@jovenindo.com` / `qwe123`.
