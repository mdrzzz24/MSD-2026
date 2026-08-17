# Prompt Revisi: Aplikasi React Native GENERIK (config-driven) — Login, Tracking & Scanning

> **Revisi dari prompt sebelumnya.** Perbedaan utama: aplikasi TIDAK menganggap
> "Agenda" / "Booth" wajib ada. Semua fitur (daftar untuk tracking, aksi scan,
> label, dan nama field JSON) ditentukan oleh **konfigurasi** yang bisa diubah,
> sehingga app bisa dipakai di sistem lain yang modulnya berbeda.
>
> Copy-paste seluruh bagian "## Prompt Revisi (salin dari sini)" ke AI coding
> assistant.

---

## Prompt Revisi (salin dari sini)

Buatkan **aplikasi mobile React Native (Expo)** berbasis **konfigurasi
(config-driven)** untuk staf acara, bernama **Event Checker**. Aplikasi dipakai
di handphone untuk: **Login**, **Tracking** (melihat daftar item), dan
**Scanning** QR peserta sebagai fitur utama.

**Prinsip paling penting: aplikasi GENERIK.** App tidak boleh menganggap ada
entitas bernama "Agenda", "Booth", "Workshop", "Sesi", dst. Semua itu hanyalah
contoh. Di sistem lain, modul & namanya bisa berbeda. Semua perilaku dikendalikan
oleh **konfigurasi JSON** (dapat diubah dari dalam app), bukan hardcode.

### 1. Arsitektur: app = shell generik + konfigurasi

Aplikasi hanya punya kerangka (shell) yang membaca **Konfigurasi** dan
menerjemahkannya menjadi menu, daftar, dan aksi scan. Konfigurasi disimpan di
`AsyncStorage`, bisa di-import/scan/diubah lewat halaman "Pengaturan".

Struktur konfigurasi (contoh — ini untuk sistem MSD26, sistem lain tinggal
ganti isinya):

```json
{
  "app_name": "Event Checker",
  "base_url": "http://127.0.0.1:8000/api",

  "login": {
    "endpoint": "/login",
    "method": "POST",
    "body": { "email": "", "password": "" },
    "field": { "user": "data.user", "name": "name", "email": "email" }
  },

  "tracking": {
    "label": "Daftar",
    "list": [
      {
        "id": "agenda",
        "label": "Agenda",
        "endpoint": "/agenda",
        "query": { "admin_id": "{user.id}" },
        "list_field": "data",
        "group_by": "agenda_type",
        "groups": { "workshop": "Workshop", "track": "Track", "session": "Session" },
        "item": {
          "title": "title",
          "subtitle": "company",
          "meta": ["room", "start_time", "end_time"],
          "speakers": { "field": "speakers", "name": "name" }
        },
        "scan_action": "scan_agenda"
      },
      {
        "id": "booth",
        "label": "Booth",
        "endpoint": "/booths",
        "list_field": "data",
        "item": { "title": "name", "subtitle": "description", "meta": ["visitor_count"] },
        "scan_action": "scan_booth"
      }
    ]
  },

  "scan_actions": {
    "scan_agenda": {
      "label": "Scan Kehadiran Sesi",
      "endpoint": "/agenda/{item_id}/scan",
      "method": "POST",
      "body": { "qr_token": "", "admin_id": "{user.id}" },
      "success_statuses": ["approved", "not_applicable"],
      "warning_statuses": { "not_registered": "Belum terdaftar di sesi ini", "pending": "Pendaftaran belum disetujui" },
      "register_action": "register_workshop",
      "register_warning": ["not_registered"],
      "registrant_field": "data.registrant"
    },
    "scan_booth": {
      "label": "Scan Booth",
      "endpoint": "/booths/{item_id}/scan",
      "method": "POST",
      "body": { "qr_token": "" },
      "registrant_field": "data.registrant"
    },
    "scan_registration": {
      "label": "Scan Registrasi (Badge)",
      "endpoint": "/registration/scan",
      "method": "POST",
      "body": { "qr_token": "" },
      "registrant_field": "data.registrant"
    },
    "register_workshop": {
      "label": "Daftarkan ke Workshop",
      "endpoint": "/workshops/{item_id}/register",
      "method": "POST",
      "body": { "qr_token": "" },
      "registrant_field": "data.registrant",
      "needs_item_id": true,
      "item_id_field": "data.workshop.id"
    }
  },

  "registrant_fields": {
    "name": { "path": "name", "aliases": ["nama", "full_name"] },
    "email": { "path": "email", "aliases": [] },
    "company": { "path": "company", "aliases": ["perusahaan", "vendor"] },
    "job_title": { "path": "job_title", "aliases": ["jabatan", "position", "role"] }
  }
}
```

### 2. Perilaku wajib (config-driven)

1. **Pertama kali buka** → halaman "Pengaturan Server": input `base_url` +
   tombol "Tes Koneksi". **Tidak ada asumsi endpoint** — tes memakai endpoint
   pertama dari `tracking.list[0].endpoint` (kalau ada) atau endpoint `login`.
2. **Login** → pakai `login.endpoint`, `login.method`, `login.body`,
   `login.field` untuk membaca user dari response. Nama field dibaca dari
   konfigurasi (misal `data.user`, `name`), bukan hardcode.
   **Simpan `user.id` dari response login** — ganti semua placeholder
   `{user.id}` di `query`/`body` konfigurasi dengan id tsb pada setiap request
   (misal `GET /agenda?admin_id={user.id}`). Untuk akun ruangan, server membatasi
   daftar/scan ke sesi yang di-assign super admin (tidak di-assign → semua sesi).
3. **Beranda / Tracking** → render menu dari `tracking.list`. Untuk tiap item:
   - Tampilkan label dari `label`.
   - Fetch `endpoint` (tambahkan `query` dari konfigurasi sebagai query string),
     ambil array dari `list_field`.
   - Render kartu memakai mapping `item` (title/subtitle/meta/speakers).
   - `group_by` + `groups` hanya dipakai kalau ada di konfigurasi; kalau tidak
     ada, tampilkan daftar flat.
   - **Kalau `tracking.list` kosong → menu tracking disembunyikan sama sekali.**
4. **Scanning** → satu layar scanner (expo-camera) yang membaca `qr_token` +
   fallback input manual.
   - Aksi scan yang tersedia diambil dari konfigurasi (`scan_actions`), dan
     dikaitkan ke konteks item tracking via `scan_action` / `needs_item_id`.
   - Panggil `endpoint` (ganti `{item_id}` dengan id item dari daftar bila ada),
     kirim `body`, baca `registrant_field`, render kartu hasil dengan field dari
     `registrant_fields` (path + aliases).
   - `success_statuses` → tampilkan sukses; `warning_statuses` → tampilkan
     peringatan & TIDAK dihitung sukses; selain itu → error.
   - **Register cepat (opsional):** jika aksi punya `register_action` dan hasil
     scan adalah warning dengan key di `register_warning` (mis. `not_registered`),
     tampilkan tombol "Register" yang memanggil aksi `register_action` dengan
     `qr_token` yang sama + `{item_id}` dari item (field `item_id_field`, fallback
     `workshop_id`), lalu tampilkan hasilnya.
5. **Riwayat scan lokal** → log generik (timestamp, label aksi, nama dari
   `registrant_fields.name`, status sukses/peringatan/error).

### 3. Stack & struktur

- **React Native + Expo** (`npx create-expo-app`), TypeScript.
- Navigasi: `@react-navigation/native` (stack + bottom tabs).
- Networking: `axios` — base URL & endpoint dibaca dari konfigurasi saat request
  (JANGAN hardcode path di komponen).
- **Helper "read by path + aliases"**: fungsi `getByPath(obj, "data.user.name")`
  dan `pickField(obj, {path, aliases})` untuk membaca nama field yang beda-beda.
- Storage: `@react-native-async-storage/async-storage` (simpan konfigurasi,
  sesi user, riwayat).
- Scanner: `expo-camera`; input manual kode sebagai fallback.
- Model TypeScript: `AppConfig`, `TrackingItem`, `ScanAction`, `ScanResult`,
  `User`, `LogEntry` — semua field opsional (`?`).
- UI: komponen reusable (Button, Card, Field, Modal hasil scan, EmptyState).
- Siapkan `README.md` (cara jalankan + cara ganti konfigurasi sistem lain).

### 4. Yang TIDAK boleh dilakukan

- ❌ Jangan membuat komponen/menu hardcode bernama "Agenda", "Booth", dsb.
- ❌ Jangan menganggap `data` pasti array, atau `data.registrant` pasti ada —
  selalu lewat `getByPath` + cek kosong.
- ❌ Jangan hardcode label menu — semua label dari `label` di konfigurasi.
- ❌ Jangan hardcode path field JSON — selalu dari `registrant_fields`/
  `login.field`/`item` di konfigurasi.
- ❌ Jangan hardcode base URL di lebih dari satu file konfigurasi.

### 5. Konfigurasi default (MSD26)

Sertakan konfigurasi default MSD26 seperti contoh di atas, dan sediakan tombol
"Reset ke Default" di halaman Pengaturan.

---

## Catatan tambahan

- **Emulator Android**: `localhost` host-nya `10.0.2.2` (contoh
  `http://10.0.2.2:8000/api`).
- **Handphone fisik**: pakai IP LAN komputer + `php artisan serve --host=0.0.0.0`.
- **QR token contoh valid**: `8dba725e73fd0665`, `0044b2e37ad75ec7`.
- **Login contoh (users)**: `rozan.chaidir@jovenindo.com` / `qwe123`.
- Endpoint MSD26 yang dipakai konfigurasi default:
  - `POST /login` — validasi dari tabel users, tidak ada token.
  - `GET /agenda` — daftar sesi; `GET /booths` — daftar booth.
  - `POST /registration/scan` — check-in + print badge (MQTT).
  - `POST /agenda/{id}/scan` — check-in sesi (gate workshop:
    `approved` = hadir; `not_registered`/`pending` = peringatan).
  - `POST /booths/{id}/scan` — check-in booth.
  - `POST /workshops/{id}/register` — daftar workshop (langsung approved).
