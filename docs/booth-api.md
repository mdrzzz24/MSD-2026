# Booth Scan API — untuk aplikasi Android

Endpoint untuk aplikasi Android melakukan tracking peserta dengan menscan QR Code di booth.

- **Base URL:** `https://<domain>/api` (mis. lokal `http://localhost:8000/api`)
- **Format:** JSON.
- **Tanpa autentikasi** — semua endpoint terbuka. Login cukup memvalidasi email & password terhadap tabel `users` (tidak mengeluarkan token).
- QR peserta berisi `qr_token` (atau `unique_code`) — kirimkan nilai mentah hasil scan sebagai `qr_token`.

---

## 1. Login (validasi akun dari tabel `users`)

`POST /api/login`

Body:
```json
{
  "email": "operator@company.com",
  "password": "********"
}
```

Response (200):
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "user": {
      "id": 36,
      "name": "Operator",
      "email": "operator@company.com",
      "is_admin": true,
      "role": "admin"
    }
  }
}
```

Error: `401` jika email/password tidak cocok dengan tabel `users`.

> Catatan: karena API tidak memakai token, hasil login hanya untuk memvalidasi akun operator. Endpoint berikutnya bisa langsung dipanggil.

---

## 2. Daftar booth

`GET /api/booths`

Response (200):
```json
{
  "success": true,
  "data": [
    { "id": 1, "name": "AWS", "description": null, "is_active": true, "order": 0, "visitor_count": 0, "created_at": "2026-08-08T22:59:19.000000Z" }
  ]
}
```

---

## 3. Scan QR peserta di booth

`POST /api/booths/{booth_id}/scan`

Body:
```json
{ "qr_token": "<nilai QR yang di-scan>" }
```

Response:

**Visit baru (200):**
```json
{
  "success": true,
  "message": "Visit recorded.",
  "already_visited": false,
  "data": {
    "registrant": { "id": 445, "name": "Herdy Permana", "email": "herdydwi@marui.co.id", "phone": "+6281511995787", "company": "Marui solusindo", "job_title": "Supervisor" },
    "visited_at": "2026-08-08T23:08:17.645781Z"
  }
}
```

**Sudah pernah scan di booth ini (200):**
```json
{
  "success": true,
  "message": "Already visited this booth.",
  "already_visited": true,
  "data": { "registrant": { "id": 445, "name": "Herdy Permana", "email": "herdydwi@marui.co.id" }, "visited_at": "2026-08-08T23:08:17.000000Z" }
}
```

**QR tidak dikenal (404):**
```json
{ "success": false, "message": "Invalid QR code. Registrant not found." }
```

**Peserta belum approved (403):**
```json
{ "success": false, "message": "Registrant is not approved." }
```

---

## 4. (Opsional) Check-in agenda

`POST /api/agenda/{agenda_item_id}/scan` — pola sama seperti scan booth, untuk check-in sesi.

---

## Contoh `curl`
```bash
# 1) validasi login (opsional)
curl -X POST https://domain/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"operator@company.com","password":"********"}'

# 2) scan booth id=1 (tanpa token)
curl -X POST https://domain/api/booths/1/scan \
  -H "Content-Type: application/json" \
  -d '{"qr_token":"0044b2e37ad75ec7"}'
```
