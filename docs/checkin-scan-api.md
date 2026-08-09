# Room / Registration Scan API — untuk aplikasi Android

Endpoint untuk men-scan QR peserta saat mereka masuk **Registrasi**, **ruangan Track**, atau **Workshop**.

- **Base URL:** `https://<domain>/api` (lokal `http://127.0.0.1:8000/api`)
- **Format:** JSON, **tanpa autentikasi**.
- QR peserta berisi `qr_token` (atau `unique_code`) — kirim nilai mentah hasil scan sebagai `qr_token`.

---

## 0. Sync SCAN OFFLINE (batch upload saat kembali online)

`POST /api/sync/scans`

Untuk kasus app scan **saat offline**: hasil scan disimpan lokal (QR + waktu
scan), lalu di-upload batch di sini setelah internet kembali.

Body:
```json
{
  "scans": [
    {
      "client_id": "a1b2c3-...",
      "action": "agenda_scan",
      "qr_token": "8dba725e73fd0665",
      "item_id": 94,
      "scanned_at": "2026-08-09T10:00:00+07:00"
    }
  ]
}
```

- `action`: `registration_scan` | `agenda_scan` | `agenda_trackout` | `booth_scan` | `workshop_register`
- `item_id`: id agenda/booth/workshop sesuai action (kosong utk `registration_scan`)
- `scanned_at` (opsional): waktu scan asli — dipakai sebagai waktu check-in/visit/track-out.

Response (200) — hasil per item, cocokkan lewat `client_id`:
```json
{
  "success": true,
  "data": [
    { "client_id": "a1b2c3-...", "action": "agenda_scan", "success": true,
      "message": "Check-in recorded.", "registration": "approved",
      "registrant": { "id": 16, "name": "Dwiky Restu Nugroho", "email": "...", "company": "...", "job_title": "Supervisor" },
      "visited_at": "2026-08-09T10:00:00.000000Z" },
    { "client_id": "x9y8z7-...", "action": "agenda_scan", "success": false,
      "message": "Registrant is not registered for this workshop.", "registration": "not_registered" }
  ]
}
```

Sifat:
- **Idempoten** — upload ulang tidak menggandakan data (`already_* = true`).
- `registration_scan` saat sync **tidak** trigger MQTT badge (hanya mencatat kehadiran).
- `agenda_scan` workshop tetap memakai gate (hanya `approved` yang sukses).
- `workshop_register` → daftar + langsung approve.

---

## 1. Scan saat REGISTRASI (print badge + check-in)

`POST /api/registration/scan`

Body:
```json
{ "qr_token": "8dba725e73fd0665" }
```

Opsional — tentukan printer admin mana yang di-trigger:
```json
{ "qr_token": "8dba725e73fd0665", "admin_id": 1 }
```
> `admin_id` = id user admin yang punya printer (topic `print/admin-{admin_id}`). Kalau tidak dikirim, default ke super admin pertama.

Response (200):
```json
{
  "success": true,
  "message": "Check-in recorded. Badge print triggered.",
  "already_checked_in": false,
  "printed": true,
  "mqtt_enabled": true,
  "data": {
    "registrant": { "id": 16, "name": "Dwiky Restu Nugroho", "email": "dwiky.restu@dplamongan.co.id", "phone": "+6282334429200", "company": "PT Dok Pantai Lamongan", "job_title": "Supervisor" },
    "checked_in_at": "2026-08-08T23:24:29.000000Z"
  }
}
```

- Perilaku sama seperti **Onsite Event**: mem-publish badge via **MQTT** (`print/admin-{id}`) dan menandai peserta **check-in** (`registrants.checked_in_at`).
- Scan ulang → `already_checked_in: true`, badge tetap di-print ulang.
- QR tidak dikenal → `404`; peserta belum approved → `403`.

---

## 2. Scan saat masuk RUANGAN Track / Workshop (check-in sesi)

`POST /api/agenda/{agenda_item_id}/scan`

Body:
```json
{ "qr_token": "8dba725e73fd0665" }
```

Response (200, visit baru):
```json
{
  "success": true,
  "message": "Check-in recorded.",
  "already_visited": false,
  "registration": "registered",
  "data": {
    "registrant": { "id": 16, "name": "Dwiky Restu Nugroho", "email": "dwiky.restu@dplamongan.co.id", "phone": "...", "company": "...", "job_title": "Supervisor" },
    "visited_at": "2026-08-08T23:24:29.165029Z"
  }
}
```

- Scan ulang sesi yang sama → `already_visited: true` (tidak dobel).
- `{agenda_item_id}` = id sesi (Track/Workshop/General) di tabel `agenda_items`. Ambil dari endpoint `GET /api/agenda`.

**Gate registrasi untuk Workshop:**
Saat scan sesi **workshop**, peserta **harus sudah terdaftar & disetujui** (`registrant_workshop.status = approved`) pada workshop tsb. Kalau belum, API mengembalikan `403` dan **tidak mencatat** kehadiran:

```json
// Belum terdaftar di workshop ini
{ "success": false, "message": "Registrant is not registered for this workshop.", "registration": "not_registered" }

// Sudah daftar tapi belum disetujui (pending)
{ "success": false, "message": "Registrant is not yet approved for this workshop.", "registration": "pending" }
```

Kalau sudah terdaftar & approved → `200`, check-in dicatat (`registration: "approved"`). Sesi Track/General bersifat walk-in (tanpa gate).

### 2b. Track OUT dari sesi (mencatat waktu keluar)

`POST /api/agenda/{agenda_item_id}/trackout`

Body:
```json
{ "qr_token": "8dba725e73fd0665" }
```

Response (200, track-out baru):
```json
{
  "success": true,
  "message": "Track-out recorded.",
  "already_tracked_out": false,
  "data": {
    "registrant": { "id": 16, "name": "Dwiky Restu Nugroho", "email": "...", "company": "...", "job_title": "Supervisor" },
    "visited_at": "2026-08-08T23:24:29.165029Z",
    "left_at": "2026-08-08T23:50:00.000000Z"
  }
}
```

- Hanya bisa track-out kalau peserta **sudah check-in** ke sesi tsb; kalau belum → `409` (`Registrant has not checked in to this session yet.`).
- Track-out ulang → `200` dengan `already_tracked_out: true`.
- QR tidak dikenal → `404`; peserta belum approved → `403`.
- **Sync offline**: gunakan action `agenda_trackout` di `POST /api/sync/scans` (item_id = agenda item id).

---

## 3. Daftar agenda (untuk tahu id sesi per ruangan)

`GET /api/agenda`

Response (200) — tiap item berisi:
- `id`, `title`, `topic_headline`, `description`
- `agenda_type` (`track` / `workshop` / `session`)
- **`company`** — vendor/company di balik sesi (konsisten): Workshop → nama workshop (mis. `Sangfor`), Track → nama track (mis. `Anaplan`, `AWS`), General keynote → nama vendor (mis. `Red Hat`); kosong untuk sesi break/placeholder.
- `workshop_name`, `track_name` — nama workshop/track jika ada
- `room`, `start_time`, `end_time`, `capacity`, `is_registrable`
- `speakers` — `id`, `name`, `title`, `photo`

---

## Ringkasan skenario
| Skenario | Endpoint | Catatan |
|----------|----------|---------|
| Peserta tiba di registrasi | `POST /api/registration/scan` | print badge via MQTT + check-in |
| Masuk ruangan Track | `POST /api/agenda/{id}/scan` | id = agenda item track |
| Masuk ruangan Workshop | `POST /api/agenda/{id}/scan` | id = agenda item workshop |

## Contoh `curl`
```bash
# registrasi
curl -X POST https://domain/api/registration/scan \
  -H "Content-Type: application/json" \
  -d '{"qr_token":"8dba725e73fd0665"}'

# track/workshop
curl -X POST https://domain/api/agenda/154/scan \
  -H "Content-Type: application/json" \
  -d '{"qr_token":"8dba725e73fd0665"}'
```
