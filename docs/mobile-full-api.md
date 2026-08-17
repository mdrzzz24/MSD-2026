# MSD26 Mobile App — Full API Reference

Semua endpoint untuk aplikasi mobile **Event Checker** (React Native/Expo).
Base URL default: `https://<domain>/api` (lokal `http://127.0.0.1:8000/api`).
Format: **JSON**, **tanpa autentikasi** (login cukup menyimpan objek `user`).

Envelope response selalu: `{ "success": bool, "message": string, "data": ... }`.

---

## Ringkasan endpoint

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| POST | `/api/login` | Login admin (tabel `users`, tanpa token) |
| GET  | `/api/config` | Konfigurasi app + status MQTT + daftar printer |
| GET  | `/api/mqtt/status` | Status broker MQTT + topic printer per admin |
| POST | `/api/mqtt/test` | Test print ke printer admin (verifikasi rantai MQTT) |
| GET  | `/api/agenda` | Daftar agenda/sesi |
| GET  | `/api/booths` | Daftar booth |
| POST | `/api/registration/scan` | **Scan registrasi → print badge (MQTT) + check-in** |
| POST | `/api/agenda/{id}/scan` | Scan kehadiran sesi/workshop |
| POST | `/api/agenda/{id}/trackout` | Catat waktu keluar sesi |
| POST | `/api/booths/{id}/scan` | Scan kunjungan booth |
| POST | `/api/workshops/{id}/register` | Daftarkan peserta ke workshop (langsung approved) |
| GET  | `/api/agenda/{id}/attendees` | Daftar peserta hadir di sesi |
| GET  | `/api/booths/{id}/attendees` | Daftar pengunjung booth |
| GET  | `/api/activity` | Feed aktivitas scan/print terbaru |
| POST | `/api/sync/scans` | Upload batch scan offline |

> QR peserta berisi `qr_token` (atau `unique_code`) — kirim nilai mentah hasil
> scan sebagai `qr_token`. Token valid contoh: `8dba725e73fd0665`.

---

## 0. Akun ruangan (Room Account) & pembatasan sesi

Aplikasi mobile bisa dipakai oleh **akun ruangan** — satu akun per ruangan
(`role = "room"`, contoh email `ballroom-a@msd26.app`, `java@msd26.app`).
Super admin membatasi sesi mana yang boleh di-track tiap akun melalui halaman
**Room Accounts** (`/admin/room-accounts`):

- Akun ruangan **tanpa sesi yang ditugaskan** → boleh menampilkan & men-track
  **SEMUA sesi** (perilaku default).
- Akun ruangan **yang sudah ditugaskan sesi** → hanya sesi-sesi tsb yang tampil
  dan bisa di-scan / di-track-out / dilihat attendee-nya.
- Non-akun-ruangan (admin biasa) atau tanpa `admin_id` → tetap melihat semua
  (perilaku lama, API tetap terbuka).

Cara kerja: login → simpan `user.id` sebagai `admin_id`, lalu sertakan
`admin_id` di setiap endpoint agenda di bawah. Server memakai `admin_id` untuk
membatasi hasil sesuai tugas super admin.

---

## 1. Login

```
POST /api/login
Body: { "email": "admin@example.com", "password": "..." }
```

`200` → `{ "success": true, "data": { "user": { "id": 1, "name": "...", "email": "...", "is_admin": true, "role": "super_admin", "room": null } } }`
`401` → `{ "success": false, "message": "Invalid credentials." }`

Untuk akun ruangan (`role = "room"`) field `room` berisi `{ "id": 4, "name": "Sumatra" }`
(room yang diikatkan); untuk role lain `room` = `null`.

> **Penting:** `user.id` dipakai sebagai `admin_id` saat scan registrasi, supaya
> badge di-print ke printer admin yang login (topic `print/admin-{id}`).
> Untuk akun ruangan, `user.id` juga dipakai sebagai `admin_id` pada endpoint
> agenda supaya daftar sesi & scan dibatasi sesuai penugasan super admin.
>
> Contoh alur login:
> ```bash
> curl -X POST https://domain/api/login \
>   -H "Content-Type: application/json" \
>   -d '{"email":"admin@example.com","password":"..."}'
> # → data.user.id = 12  →  gunakan 12 sebagai admin_id di endpoint berikut
> ```

---

## 2. Konfigurasi app

```
GET /api/config?admin_id={userId}
```

- `admin_id` **(opsional)** — id user yang sedang login (dari `/api/login`).
  `default_topic` **mengikuti user ini**, mis. `admin_id=12` → `print/admin-12`.
  Bisa dikirim via query (`?admin_id=12`) atau JSON body (`{"admin_id":12}`).
- Tanpa `admin_id` / id tidak valid → fallback ke super admin pertama (`print/admin-1`).

`200` → `{ "success": true, "data": { "app": {...}, "mqtt": {...}, "printers": [...], "server_time": "..." } }`

- `app.base_url` / `api_base_url` — alamat server aktif.
- `app.room` — untuk akun ruangan: `{ "id", "name" }`; selain itu `null`.
- `app.scope` — ringkasan pembatasan sesi akun yang login:
  `{ "type": "all"|"assigned"|"unrestricted", "agenda_item_ids": [...] }`
  (`type="all"` = akun ruangan tanpa penugasan → semua sesi; `type="assigned"` =
  hanya id sesi di `agenda_item_ids`; `unrestricted` = bukan akun ruangan).
- `mqtt.enabled` — apakah MQTT aktif; `mqtt.default_admin_id` / `mqtt.default_topic` —
  printer **user yang login** (mis. `print/admin-12`).
- `printers[]` — daftar admin & topic printer masing-masing (`{ id, name, email, role, topic }`),
  supaya app tahu printer mana yang tersedia.

Contoh — user id 12 yang login:
```bash
curl -s 'https://domain/api/config?admin_id=12'
```
```json
{
  "success": true,
  "data": {
    "app": { "name": "MSD26", "base_url": "https://domain", "api_base_url": "https://domain/api", "request_format": "json", "version": "1.0.0" },
    "mqtt": { "enabled": true, "host": "165.22.50.240", "port": 1883, "topic_prefix": "print", "default_admin_id": 12, "default_topic": "print/admin-12" },
    "printers": [ { "id": 12, "name": "Ratna Suci Pratami", "email": "...", "role": "admin", "topic": "print/admin-12" } ],
    "server_time": "2026-08-10T03:52:25+00:00"
  }
}
```

---

## 3. Status MQTT & Test Print

### 3a. Status broker + printer

```
GET /api/mqtt/status?admin_id={userId}
```

- `admin_id` **(opsional)** — id user yang login; response menyertakan
  `default_admin_id` / `default_topic` yang mengikuti user tsb (fallback super admin pertama).

`200` → `{ "success": true, "data": { "enabled": true, "host": "...", "port": 1883, "topic_prefix": "print", "default_admin_id": 12, "default_topic": "print/admin-12", "printers": [ { "id", "name", "role", "topic" } ] } }`

### 3b. Test print (verifikasi rantai server → broker → printer)

```
POST /api/mqtt/test
Body: { "admin_id": 12, "name": "Test Print", "company": "MQTT Test" }
```

- `admin_id` (opsional) — printer admin mana yang di-test (id user yang login);
  default super admin pertama. Tidak valid → fallback super admin pertama.
- `name` / `company` (opsional) — teks yang dicetak (default "Test Print" / "MQTT Test").

`200` → `{ "success": true, "message": "Test print published to print/admin-12.", "published": true, "mqtt_enabled": true, "topic": "print/admin-12" }`
`422` → `{ "success": false, "message": "MQTT is not enabled or the broker is unreachable.", "published": false, "mqtt_enabled": false, "topic": "..." }`

> Setiap test print dicatat ke `scan_logs` (action `mqtt_test`) dan tampil di `/api/activity`.

---

## 4. Daftar agenda & booth

```
GET /api/agenda?admin_id={userId}
GET /api/booths
```

- `admin_id` **(opsional)** — untuk **akun ruangan**, daftar hanya berisi sesi
  yang ditugaskan ke akun tsb (lihat §0). Tanpa `admin_id` / bukan akun ruangan →
  semua sesi.
- Response `/api/agenda` menyertakan (di luar `data`):
  - `scope` — ringkasan pembatasan (lihat §2).
  - `room` — `{ id, name }` ruangan yang diikat ke akun ruangan; `null` untuk
    non-akun-ruangan. Bisa dibaca langsung dari `/agenda` (tanpa `/config`).
  - `rooms` — daftar nama ruangan (string) yang unik dari sesi-sesi yang bisa
    di-manage akun tsb (yaitu ruangan yang muncul di `data`).

`/api/agenda` tiap item: `id`, `title`, `topic_headline`, `description`, `agenda_type`
(`track`/`workshop`/`session`), `workshop_id`, `track_id`, `company`, `workshop_name`,
`track_name`, `room`, `start_time`, `end_time`, `capacity`, `is_registrable`,
`speakers[]` (`id`, `name`, `title`, `photo`).

`/api/booths` tiap item: `id`, `name`, `description`, `is_active`, `order`, `visitor_count`.

---

## 5. Scan REGISTRASI (print badge + check-in)

```
POST /api/registration/scan
Body: { "qr_token": "8dba725e73fd0665" }
Body (pilih printer — gunakan id user yang login): { "qr_token": "...", "admin_id": 12 }
```

Perilaku persis **Onsite Event**: mem-publish badge via MQTT ke
`print/admin-{admin_id}` dan menandai peserta check-in (`registrants.checked_in_at`).

`200` → `{ "success": true, "message": "Check-in recorded. Badge print triggered.", "already_checked_in": false, "printed": true, "mqtt_enabled": true, "data": { "registrant": { "id", "name", "email", "phone", "company", "job_title" }, "checked_in_at": "..." } }`

- Scan ulang → `already_checked_in: true` (badge tetap di-print ulang).
- QR tidak dikenal → `404`; peserta belum approved → `403`.
- Setiap scan tercatat di `scan_logs` (action `registration_scan`, `printed` = apakah badge terkirim).

---

## 6. Scan kehadiran sesi / workshop

```
POST /api/agenda/{agenda_item_id}/scan
Body: { "qr_token": "8dba725e73fd0665" }
Body (akun ruangan): { "qr_token": "...", "admin_id": 27 }
```

`200` (hadir) → `{ "success": true, "message": "Check-in recorded.", "already_visited": false, "registration": "approved"|"not_applicable", "data": { "registrant": {...}, "visited_at": "..." } }`
`403` (belum terdaftar workshop) → `{ "success": false, "message": "Registrant is not registered for this workshop.", "registration": "not_registered" }`
`403` (pending) → `{ "success": false, "message": "Registrant is not yet approved for this workshop.", "registration": "pending" }`
`403` (bukan sesi akun tsb) → `{ "success": false, "message": "This session is not assigned to your account.", "scope": "forbidden" }`

- Untuk sesi **workshop**: hanya dicatat jika `registration === "approved"`.
- Sesi **track/general**: walk-in (`registration: "not_applicable"`).
- Scan ulang sesi sama → `already_visited: true`.

### Track-out (waktu keluar)

```
POST /api/agenda/{agenda_item_id}/trackout
Body: { "qr_token": "8dba725e73fd0665" }
Body (akun ruangan): { "qr_token": "...", "admin_id": 27 }
```

`200` → `{ "success": true, "message": "Track-out recorded.", "already_tracked_out": false, "data": { "registrant": {...}, "visited_at": "...", "left_at": "..." } }`
- Belum check-in → `409`; sudah track-out → `already_tracked_out: true`.
- Akun ruangan di luar sesi → `403` `"This session is not assigned to your account."`
  (`scope: "forbidden"`).

---

## 7. Scan booth

```
POST /api/booths/{booth_id}/scan
Body: { "qr_token": "8dba725e73fd0665" }
```

`200` → `{ "success": true, "message": "Visit recorded.", "already_visited": false, "data": { "registrant": {...}, "visited_at": "..." } }`
- Sudah pernah → `already_visited: true`.

---

## 8. Registrasi workshop (langsung approved)

```
POST /api/workshops/{workshop_id}/register
Body: { "qr_token": "..." }   ATAU   { "registrant_id": 445 }
```

`200` → `{ "success": true, "already_registered": false, "data": { "workshop": {...}, "registrant": {...}, "status": "approved" } }`
- Sudah approved → `already_registered: true`.
- `workshop_id` diambil dari field `workshop_id` pada response `/api/agenda` (bukan `id` agenda).

---

## 9. Activity feed (monitoring real-time)

```
GET /api/activity?limit=20&action=registration_scan&admin_id=27
```

`200` → `{ "success": true, "data": [ { "id", "action", "registrant_id", "registrant_name", "item_id", "item_type", "item_label", "source", "client_id", "admin_id", "success", "printed", "message", "created_at" } ] }`

- `limit` (1–100, default 20), `action` (opsional) untuk filter:
  `registration_scan` | `agenda_scan` | `agenda_trackout` | `booth_scan` | `workshop_register` | `mqtt_test`.
- `admin_id` (opsional) — untuk **akun ruangan**, hanya aktivitas sesi yang
  ditugaskan ke akun tsb yang dikembalikan.
- `source`: `mobile` (scan langsung) / `sync` (upload offline) / `web`.
- `printed`: apakah badge MQTT berhasil dikirim.

Contoh:
```json
{
  "success": true,
  "data": [
    { "id": 1, "action": "mqtt_test", "registrant_id": null, "registrant_name": null, "item_type": "printer", "item_label": "print/admin-1", "source": "mobile", "admin_id": 1, "success": true, "printed": true, "message": "Test print published.", "created_at": "2026-08-10T03:52:44+00:00" }
  ]
}
```

---

## 10. Sync OFFLINE (upload batch saat kembali online)

```
POST /api/sync/scans
Body: {
  "admin_id": 27,
  "scans": [
    { "client_id": "a1b2c3-...", "action": "agenda_scan", "qr_token": "8dba725e73fd0665", "item_id": 94, "scanned_at": "2026-08-09T10:00:00+07:00" }
  ]
}
```

- `admin_id` (opsional) — untuk **akun ruangan**, tiap `agenda_scan`/`agenda_trackout`
  divalidasi: sesi di luar penugasan akun → `success: false`, `message: "This session
  is not assigned to your account."`, `scope: "forbidden"`.
- `action`: `registration_scan` | `agenda_scan` | `agenda_trackout` | `booth_scan` | `workshop_register`
- `item_id`: id agenda/booth/workshop sesuai action (kosong utk `registration_scan`)
- `scanned_at` (opsional): waktu scan asli — dipakai sebagai waktu check-in/visit/track-out.
- **Idempoten** — upload ulang tidak menggandakan data (`already_* = true`).
- `registration_scan` saat sync **tidak** trigger MQTT (hanya catat kehadiran).
- Setiap item sync tercatat di `scan_logs` (source `sync`).

Response (200) — hasil per item, cocokkan lewat `client_id`:
```json
{
  "success": true,
  "data": [
    { "client_id": "a1b2c3-...", "action": "agenda_scan", "success": true, "message": "Check-in recorded.", "registration": "approved", "registrant": { "id": 16, "name": "...", "company": "...", "job_title": "..." }, "visited_at": "..." },
    { "client_id": "x9y8z7-...", "action": "agenda_scan", "success": false, "message": "Registrant is not registered for this workshop.", "registration": "not_registered" }
  ]
}
```

---

## Contoh `curl`

```bash
# konfigurasi + status printer
curl -s http://127.0.0.1:8000/api/config
curl -s http://127.0.0.1:8000/api/mqtt/status

# test print ke printer milik user yang login (id 12)
curl -X POST http://127.0.0.1:8000/api/mqtt/test \
  -H "Content-Type: application/json" \
  -d '{"admin_id":12,"name":"Test Print","company":"MQTT Test"}'
# → {"success":true,"message":"Test print published to print/admin-12.","published":true,"mqtt_enabled":true,"topic":"print/admin-12"}

# registrasi (print badge ke printer user 12 + check-in)
curl -X POST http://127.0.0.1:8000/api/registration/scan \
  -H "Content-Type: application/json" \
  -d '{"qr_token":"8dba725e73fd0665","admin_id":12}'

# track / workshop
curl -X POST http://127.0.0.1:8000/api/agenda/154/scan \
  -H "Content-Type: application/json" \
  -d '{"qr_token":"8dba725e73fd0665"}'

# activity feed
curl -s "http://127.0.0.1:8000/api/activity?limit=20"
```
