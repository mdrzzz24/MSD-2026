# API Track Out Session — Contoh Payload & Response

Mencatat **waktu keluar** peserta dari sebuah sesi/workshop (agenda item). Data
disimpan di kolom `left_at` pada tabel `agenda_visits` (satu baris yang sama
dengan check-in).

- **Base URL:** `https://<domain>/api` (lokal `http://127.0.0.1:8000/api`)
- **Format:** JSON, **tanpa autentikasi**, method **POST**.

---

## 1. Track Out (pertama kali)

```
POST /api/agenda/{agenda_item_id}/trackout
Content-Type: application/json
```

Payload:
```json
{
  "qr_token": "0044b2e37ad75ec7"
}
```

Response `200` — `already_tracked_out: false`:
```json
{
  "success": true,
  "message": "Track-out recorded.",
  "already_tracked_out": false,
  "data": {
    "registrant": {
      "id": 445,
      "name": "Herdy Permana",
      "email": "herdydwi@marui.co.id",
      "phone": "+6281511995787",
      "company": "Marui solusindo",
      "job_title": "Supervisor"
    },
    "visited_at": "2026-08-09T10:40:09.000000Z",
    "left_at": "2026-08-09T10:40:10.000000Z"
  }
}
```

- `left_at` = waktu keluar (waktu track-out).
- `visited_at` = waktu check-in sebelumnya (tetap).

---

## 2. Track Out ulang (sudah pernah track out)

Response `200` — `already_tracked_out: true`:
```json
{
  "success": true,
  "message": "Registrant has already been tracked out of this session.",
  "already_tracked_out": true,
  "data": {
    "registrant": { "id": 445, "name": "Herdy Permana", "email": "herdydwi@marui.co.id" },
    "visited_at": "2026-08-09T10:40:09.000000Z",
    "left_at": "2026-08-09T10:40:10.000000Z"
  }
}
```

---

## 3. Error cases

**Belum check-in ke sesi ini** → `409`:
```json
{ "success": false, "message": "Registrant has not checked in to this session yet." }
```

**QR tidak dikenal** → `404`:
```json
{ "success": false, "message": "Invalid QR code. Registrant not found." }
```

**Peserta belum approved** → `403`:
```json
{ "success": false, "message": "Registrant is not approved." }
```

---

## 4. Sync offline (action `agenda_trackout`)

Untuk scan offline yang di-upload belakangan via `POST /api/sync/scans`:

Payload:
```json
{
  "scans": [
    {
      "client_id": "to-001",
      "action": "agenda_trackout",
      "qr_token": "0044b2e37ad75ec7",
      "item_id": 1,
      "scanned_at": "2026-08-09T12:00:00+07:00"
    }
  ]
}
```

Response (per item):
```json
{
  "success": true,
  "data": [
    {
      "client_id": "to-001",
      "action": "agenda_trackout",
      "success": true,
      "message": "Track-out recorded.",
      "already_tracked_out": false,
      "registrant": { "id": 445, "name": "Herdy Permana", "email": "..." },
      "visited_at": "2026-08-09T10:40:09.000000Z",
      "left_at": "2026-08-09T12:00:00.000000Z"
    }
  ]
}
```

- `scanned_at` dipakai sebagai waktu `left_at` (bukan waktu upload).
- Idempoten: upload ulang → `already_tracked_out: true`.
- `item_id` = id agenda item sesi tersebut.

---

## Ringkasan

| Kasus | Status | `already_tracked_out` |
|-------|--------|------------------------|
| Track out baru (sudah check-in) | 200 | `false` |
| Track out ulang | 200 | `true` |
| Belum check-in | 409 | — |
| QR tidak dikenal | 404 | — |
| Peserta belum approved | 403 | — |
