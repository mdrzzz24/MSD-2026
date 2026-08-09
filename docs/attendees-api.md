# API Lihat Attendee Booth / Session — Contoh Payload & Response

Dua endpoint untuk melihat **daftar peserta (attendee)** yang sudah check-in di
sebuah **booth** atau **sesi/workshop** (agenda item). Data diambil dari tabel
`booth_visits` dan `agenda_visits` (hasil scan).

- **Base URL:** `https://<domain>/api` (lokal `http://127.0.0.1:8000/api`)
- **Format:** JSON, **tanpa autentikasi**, method **GET**.

---

## 1. Attendee BOOTH

`GET /api/booths/{booth_id}/attendees`

Contoh request (booth id 1 = AWS):

```
GET http://127.0.0.1:8000/api/booths/1/attendees
```

Response `200`:
```json
{
  "success": true,
  "data": {
    "booth": {
      "id": 1,
      "name": "AWS"
    },
    "total": 4,
    "attendees": [
      {
        "id": 1427,
        "name": "Cahyanto Arie Wibowo",
        "email": "arie@maverick.co.id",
        "company": "Maverick Indonesia",
        "job_title": "Director",
        "phone": "+6281318124694",
        "visited_at": "2026-08-09T10:59:00.000000Z"
      },
      {
        "id": 1542,
        "name": "Bintang Eviyanti",
        "email": "bintang.eviyanti@danone.com",
        "company": "Danone Indonesia",
        "job_title": "Senior Manager",
        "phone": "+628111921234",
        "visited_at": "2026-08-09T10:56:49.000000Z"
      },
      {
        "id": 16,
        "name": "Dwiky Restu Nugroho",
        "email": "dwiky.restu@dplamongan.co.id",
        "company": "PT Dok Pantai Lamongan",
        "job_title": "Supervisor",
        "phone": "+6282334429200",
        "visited_at": "2026-08-09T02:37:45.000000Z"
      }
    ]
  }
}
```

- Urutan attendee: terbaru (`visited_at` DESC).
- Booth tidak ada → `404` (`No query results for model [App\Models\Booth]`).

---

## 2. Attendee SESSION / WORKSHOP (agenda item)

`GET /api/agenda/{agenda_item_id}/attendees`

Contoh request (agenda item id 95 = workshop Red Hat):

```
GET http://127.0.0.1:8000/api/agenda/95/attendees
```

Response `200`:
```json
{
  "success": true,
  "data": {
    "agenda_item": {
      "id": 95,
      "title": "Private Model as a Service - A Practical Guide to Red Hat AI",
      "type": "workshop",
      "room": "Sumatra"
    },
    "total": 2,
    "attendees": [
      {
        "id": 1542,
        "name": "Bintang Eviyanti",
        "email": "bintang.eviyanti@danone.com",
        "company": "Danone Indonesia",
        "job_title": "Senior Manager",
        "phone": "+628111921234",
        "visited_at": "2026-08-08T23:36:04.000000Z"
      },
      {
        "id": 1557,
        "name": "Arie Sunandar",
        "email": "mk.arie.sunandar@pertamina.com",
        "company": "PT.Pertamina EP Cepu",
        "job_title": "Staff",
        "phone": "+6281219848348",
        "visited_at": "2026-08-08T23:34:46.000000Z"
      }
    ]
  }
}
```

- `type` diisi otomatis: `workshop` / `track` / `session`.
- Agenda item tidak ada → `404`.

---

## 3. Kasus kosong (belum ada yang check-in)

Kalau belum ada visit, `attendees` = `[]` dan `total` = `0`:

```json
{
  "success": true,
  "data": {
    "booth": { "id": 2, "name": "Booth Baru" },
    "total": 0,
    "attendees": []
  }
}
```

---

## Ringkasan field attendee

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id` | int | id registrant |
| `name` | string | nama peserta |
| `email` | string | email |
| `company` | string \| null | perusahaan |
| `job_title` | string \| null | jabatan |
| `phone` | string \| null | no. HP |
| `visited_at` | string (ISO) | waktu scan/check-in |
