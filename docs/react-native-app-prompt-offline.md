# Prompt: Tambah Fitur Offline Queue & Auto-Sync (React Native)

> Lampiran untuk prompt revisi app **Event Checker (config-driven)**. Copy-paste
> bagian "## Prompt Tambahan (salin dari sini)" di bawah ini **setelah** prompt
> utama, atau pakai sebagai prompt refactor untuk app yang sudah terlanjur dibuat.

---

## Prompt Tambahan (salin dari sini)

Tambahkan fitur **mode offline** ke aplikasi: saat perangkat **terputus dari
jaringan**, hasil scan QR beserta **waktu scan** tetap tersimpan di perangkat.
Begitu **internet kembali**, semua scan yang tertunda **otomatis diunggah** ke
server (dan ada juga tombol "Sync" manual). Tetap **config-driven** — jangan
menganggap endpoint tertentu, gunakan `scan_actions` dari konfigurasi seperti
sebelumnya.

### 1. Konsep yang harus diimplementasikan

1. **Antrean scan lokal (ScanQueue)**
   - Simpan di `AsyncStorage` (key `scan_queue_v1`).
   - Setiap entri: `{ id, action, itemId?, qrToken, scannedAt, status,
     attempts, lastError?, syncedAt? }`.
   - `scannedAt` = ISO timestamp **waktu saat scan** (dipakai untuk tracking
     waktu kehadiran walau baru diupload belakangan).
   - Status: `pending` → `syncing` → `synced` | `failed`.

2. **Deteksi koneksi**
   - Pakai `@react-native-community/netinfo`.
   - Saat scan: kalau **online** → kirim langsung; kalau **offline** → enqueue.
   - Kalau online tapi request gagal karena jaringan (tidak ada response) →
     otomatis enqueue.

3. **Auto-sync**
   - `NetInfo` listener: saat koneksi pulih → jalankan `sync()`.
   - `sync()` juga dipanggil saat app start & dari tombol "Sync sekarang".
   - Proses antrean **FIFO, berurutan** (tidak paralel) supaya urutan kehadiran
     terjaga dan tidak membebani server.
   - Untuk tiap entri: bangun URL dari `scan_actions[action].endpoint`
     (ganti `{item_id}`), kirim `{ ...body, qr_token }`.

4. **Aturan retry**
   - Gagal karena jaringan / HTTP ≥ 500 → tetap `pending`, `attempts+1`,
     coba lagi maksimal **3 kali**.
   - Gagal karena HTTP 400/401/403/404/422 (server merespons) → `failed`
     (tidak auto-retry, karena ini error permanen seperti "QR tidak dikenal" /
     "belum terdaftar"). Simpan `lastError` untuk ditinjau.

5. **UI**
   - Di layar scan, tampilkan indikator mode **"Offline"** / **"Online"**.
   - Saat offline, hasil scan ditampilkan sebagai **"Tersimpan offline — akan
     diunggah otomatis saat online"** beserta waktu scan.
   - Badge jumlah antrean pending di header (misal "3 belum terkirim").
   - Halaman **Riwayat/Sync** menampilkan daftar antrean dengan status
     (pending/synced/failed), `scannedAt`, nama peserta (kalau sudah ada),
     `lastError`, dan tombol retry untuk yang `failed`.
   - Empty state saat antrean kosong.

6. **Helper**
   - `getByPath(obj, "data.registrant")` untuk baca field bertingkat.
   - Konfigurasi tetap dari `AsyncStorage` (`app_config`), termasuk
     `scan_actions` — TIDAK hardcode endpoint.

### 2. Struktur file yang diharapkan

```
src/services/
  scanQueue.ts      // class ScanQueue: all/enqueue/update/remove + persist AsyncStorage
  syncManager.ts    // class SyncManager: start(), checkOnline(), handleScan(), sync(), pendingCount()
  api.ts            // axios helper + buildUrl dari konfigurasi
src/types/
  scan.ts           // PendingScan, ScanActionId, ScanOutcome
src/screens/
  ScanScreen.tsx    // indikator online/offline, hasil scan offline/online
  SyncScreen.tsx    // daftar antrean + retry manual
src/components/
  OfflineBanner.tsx // banner "Offline — scan disimpan lokal"
```

### 3. Perilaku yang TIDAK boleh

- ❌ Jangan hardcode endpoint scan di komponen — selalu lewat
  `scan_actions` dari konfigurasi.
- ❌ Jangan mengupload antrean secara paralel (harus FIFO berurutan).
- ❌ Jangan menghapus entri yang `failed` tanpa konfirmasi/tinjauan.
- ❌ Jangan kehilangan `scannedAt` — itu sumber waktu kehadiran.
- ❌ Jangan auto-retry tanpa batas — batasi 3x lalu `failed`.

### 4. Dependencies tambahan

- `@react-native-community/netinfo`
- `@react-native-async-storage/async-storage` (sudah ada)
- `axios` (sudah ada)

Sertakan contoh pemakaian di `README.md`: cara menonaktifkan WiFi untuk
mengetes mode offline, lalu aktifkan lagi untuk melihat auto-sync.

---

## Catatan untuk kamu (tim developer)

- **Referensi implementasi** sudah disediakan di repo:
  `docs/mobile-reference/offline-queue.ts` — berisi `ScanQueue`, `SyncManager`,
  `buildUrl`, `getByPath`, dan contoh pemakaian. Bisa dijadikan dasar (boleh
  dimodifikasi).
- **Idempoten**: endpoint scan MSD26 aman dipanggil ulang (scan ulang →
  `already_visited: true`, tidak dobel), jadi replay antrean tidak
  menghasilkan data ganda.
- **Waktu kehadiran**: untuk akurasi, simpan `scannedAt` saat scan; kalau
  perlu, nanti server bisa ditambah kolom untuk menyimpan waktu ini dari
  payload (misal body `scanned_at`). Saat ini body hanya `qr_token` — kalau
  mau, mintakan tambahan endpoint/kolom dari backend.
