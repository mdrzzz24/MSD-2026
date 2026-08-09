# Prompt Fitur: Menu "Lihat Konfigurasi" (Config Viewer)

> **Tambahan untuk app React Native (Expo) "Event Checker" yang config-driven.**
> Fitur ini menampilkan **konfigurasi aktif** (AppConfig) yang sedang dipakai
> app — berguna untuk verifikasi setelah import QR, debugging, atau audit.
>
> Copy-paste bagian "## Prompt Fitur (salin dari sini)" ke AI coding assistant
> untuk menambahkan fitur ini ke project yang sudah ada.

---

## Prompt Fitur (salin dari sini)

Tambahkan menu **"Lihat Konfigurasi" (Config Viewer)** ke aplikasi React Native
(Expo) "Event Checker" yang bersifat config-driven.

### 1. Tujuan

Menampilkan **konfigurasi aktif** (objek `AppConfig`) yang sedang dipakai app,
supaya staf bisa memverifikasi/memeriksa isi konfigurasi yang sudah diimpor
(lewat QR, manual, atau default). Bisa juga untuk debugging saat app tidak
terhubung ke server.

### 2. Lokasi menu

- Tambahkan entri **"Lihat Konfigurasi"** di halaman **Settings**, di bawah/
  dekat menu "Import Configuration".
- Bisa juga ditambahkan sebagai aksi di halaman awal/beranda (ikon kecil).

### 3. Layar Config Viewer

Saat dibuka, tampilkan halaman berisi:

1. **Ringkasan (highlight card)** — key penting yang sering dicek:
   - `app_name`
   - `base_url`
   - `request_format` (`json` / `form`)
   - `event_id`
2. **Konten lengkap** — seluruh `AppConfig` dalam bentuk:
   - Tampilan **berformat (pretty JSON)** dalam kotak yang bisa di-scroll, ATAU
   - Tampilan **pohon (collapsible)** per bagian: `login`, `tracking`,
     `scan_actions`, `registrant_fields`.
3. **Tombol aksi:**
   - **"Salin JSON"** → salin seluruh config sebagai JSON ke clipboard.
   - **"Reset ke Default"** → kembalikan config ke default bawaan app
     (dengan konfirmasi).
   - **"Tes Koneksi"** → panggil endpoint pertama `tracking.list[0].endpoint`
     (atau endpoint `login`) untuk memastikan `base_url` aktif.

### 4. Perilaku & detail

- Data diambil dari **store config aktif** yang sama yang dipakai seluruh app
  (React Context / AsyncStorage). Jangan simpan duplikat terpisah.
- Kalau ada bagian config yang **belum pernah di-override** (masih default),
  tampilkan label kecil "default" di sampingnya.
- Tampilkan juga **sumber config** (mis. "Default", "Imported via QR",
  "Manual") dan **waktu terakhir diubah**, kalau tersedia.
- Handle config kosong / belum terisi → tampilkan pesan ramah + tombol
  "Import Configuration".
- UI konsisten dengan app: komponen `Card`, `Field`, `Button`, `Section`.

### 5. Struktur yang diharapkan

- `src/screens/SettingsScreen.tsx` — tambah entri menu "Lihat Konfigurasi".
- `src/screens/ConfigViewerScreen.tsx` — layar baru.
- `src/components/ConfigTree.tsx` (opsional) — render collapsible per bagian.
- `src/utils/configExport.ts` (opsional) — helper untuk "Salin JSON".
- Gunakan `expo-clipboard` untuk salin ke clipboard.

### 6. Yang TIDAK boleh dilakukan

- ❌ Jangan tampilkan config dari file statis — harus dari store aktif.
- ❌ Jangan menampilkan password/token apa pun yang tidak perlu; jika ada
  field sensitif di config, sembunyikan/masked.
- ❌ Jangan izinkan edit langsung dari layar ini (read-only) — edit via
  "Import Configuration" / Settings.

---

## Catatan tambahan

- Setelah fitur ini, alur verifikasi jadi: **Import QR → Lihat Konfigurasi →
  cek base_url/request_format → Tes Koneksi**.
- Contoh konfigurasi MSD26 (Laravel):
  ```json
  { "base_url": "http://127.0.0.1:8000/api", "request_format": "json", "event_id": "" }
  ```
