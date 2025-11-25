# ✅ FIX: Status Kesegaran Kadaluarsa Semuanya

## 🐛 Masalah
Semua bunga di inventory menampilkan status "Kadaluarsa" padahal tanggal kadaluarsanya masih jauh di masa depan.

---

## 🔍 Penyebab
Logic perbandingan tanggal salah:
```php
// ❌ SALAH - Logic terbalik
$daysUntilExpired = $expiredDate->diffInDays(now(), false);
if ($daysUntilExpired < 0) {
    $status = 'kadaluarsa';
}
```

Masalah:
- `diffInDays()` dengan parameter `false` menghasilkan nilai negatif yang tidak konsisten
- Perbandingan menjadi terbalik

---

## ✅ Solusi
Menggunakan metode perbandingan tanggal yang lebih jelas:

```php
// ✅ BENAR - Logic jelas
$expiredDate = \Carbon\Carbon::parse($f->expired_at);
$today = \Carbon\Carbon::today();

if ($expiredDate->lt($today)) {
    // Tanggal kadaluarsa LEBIH KECIL dari hari ini = sudah lewat
    $status = 'kadaluarsa';
} elseif ($expiredDate->diffInDays($today) <= 3) {
    // Selisih hari dengan hari ini <= 3 = segera habis
    $status = 'segera_habis';
} else {
    // Selisih hari > 3 = masih segar
    $status = 'segar';
}
```

---

## 📊 Cara Kerja

### Contoh 1: Mawar Merah (expired_at: 2025-11-29)
```
$today = 2025-11-19
$expiredDate = 2025-11-29

$expiredDate->lt($today)        → false (2025-11-29 TIDAK < 2025-11-19)
$expiredDate->diffInDays($today) → 10 (selisih 10 hari)

Kondisi:
  lt($today) = false ✓
  diffInDays($today) = 10 (> 3) ✓
  
STATUS: SEGAR ✅
```

### Contoh 2: Krisan Merah (expired_at: 2025-11-23)
```
$today = 2025-11-19
$expiredDate = 2025-11-23

$expiredDate->lt($today)        → false (2025-11-23 TIDAK < 2025-11-19)
$expiredDate->diffInDays($today) → 4 (selisih 4 hari)

Kondisi:
  lt($today) = false ✓
  diffInDays($today) = 4 (> 3) ✓
  
STATUS: SEGAR ✅
```

### Contoh 3: Bunga Expired 2025-11-21 (2 hari)
```
$today = 2025-11-19
$expiredDate = 2025-11-21

$expiredDate->lt($today)        → false (2025-11-21 TIDAK < 2025-11-19)
$expiredDate->diffInDays($today) → 2 (selisih 2 hari)

Kondisi:
  lt($today) = false ✓
  diffInDays($today) = 2 (<= 3) ✓
  
STATUS: SEGERA HABIS ⚠️
```

### Contoh 4: Bunga Expired 2025-11-18 (sudah lewat)
```
$today = 2025-11-19
$expiredDate = 2025-11-18

$expiredDate->lt($today)        → true (2025-11-18 < 2025-11-19) ✓
  
STATUS: KADALUARSA ❌
```

---

## 🎯 Hasil
Sekarang status kesegaran menampilkan:
- ✅ **Segar** (badge hijau) - expired > 3 hari lagi
- ⚠️ **Segera Habis** (badge kuning) - expired dalam 0-3 hari
- ❌ **Kadaluarsa** (badge merah) - expired sudah lewat
- ⚪ **-** (badge abu-abu) - tanggal tidak diisi

---

## 🔧 Method Reference
| Method | Penjelasan |
|--------|-----------|
| `lt($date)` | Less Than - apakah tanggal lebih kecil dari date yang diberikan |
| `gt($date)` | Greater Than - apakah tanggal lebih besar dari date yang diberikan |
| `eq($date)` | Equal - apakah tanggal sama dengan date yang diberikan |
| `diffInDays($date)` | Selisih hari antara dua tanggal (selalu positif) |

---

## ✅ STATUS
**FIXED! 🎉**

Sekarang coba buka inventory dan lihat status kesegaran yang benar:
- Mawar Merah → Segar ✅
- Krisan Merah → Segar ✅
- Lily Putih → Segar ✅
- etc...
