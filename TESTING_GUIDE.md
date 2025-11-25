# 🧪 TESTING GUIDE - Produksi & Status Kesegaran

## 📋 TESTING STEP BY STEP

### Step 1: Lihat Inventory
```
1. Buka aplikasi → Klik "Manajemen Inventory"
2. Lihat tabel bunga yang sudah di-seed:
   - Mawar Merah (50) - Expired 10 hari → Status: Segar ✅
   - Mawar Putih (30) - Expired 7 hari → Status: Segar ✅
   - Tulip Kuning (25) - Expired 5 hari → Status: Segar ✅
   - Lily Putih (20) - Expired 10 hari → Status: Segar ✅
   - Anggrek Ungu (15) - Expired 12 hari → Status: Segar ✅
   - Krisan Merah (60) - Expired 4 hari → Status: Segar ✅
   - Gerbera Pink (30) - Expired 5 hari → Status: Segar ✅
```

### Step 2: Edit Flower untuk Testing Status
```
1. Klik Edit pada Mawar Putih
2. Ubah "Tanggal Kadaluarsa" menjadi "2025-11-21" (2 hari dari sekarang)
3. Klik Update
4. Lihat status berubah menjadi "Segera Habis" ⚠️ (warna kuning)
```

### Step 3: Edit Flower untuk Status Kadaluarsa
```
1. Klik Edit pada Tulip Kuning
2. Ubah "Tanggal Kadaluarsa" menjadi "2025-11-15" (sudah lewat)
3. Klik Update
4. Lihat status berubah menjadi "Kadaluarsa" ❌ (warna merah)
```

### Step 4: Test Production - Tambah Produksi
```
1. Klik "Daftar Produksi"
2. Klik "+ Tambah Produksi"
3. Isi form:
   - Nama Produk: "Buket Pernikahan"
   - Jenis Produk: "Buket"
   - Jumlah Produksi: "2"
   - Tanggal: (today)
   - Pelanggan: "Budi"
```

### Step 5: Pilih Bahan & Jumlah
```
1. Di bagian "Pilih Bahan dari Inventory":
   - Bahan 1:
     * Pilih: "Mawar Merah (50)"
     * Jumlah: "10"
   - Klik "+ Tambah Bahan"
   - Bahan 2:
     * Pilih: "Lily Putih (20)"
     * Jumlah: "5"
```

### Step 6: Submit & Verifikasi Stock
```
1. Klik "Simpan Produksi & Kurangi Stok"
2. HARUS Muncul: Success message "Produksi berhasil dibuat dan stok berkurang!"
3. Redirect ke halaman Daftar Produksi
4. Lihat produksi baru di tabel:
   - Nama: "Buket Pernikahan"
   - Bahan: 
     * Mawar Merah (10)
     * Lily Putih (5)
```

### Step 7: Verifikasi Inventory Berkurang
```
1. Klik "Manajemen Inventory"
2. Cek stok:
   - Mawar Merah: 50 → 40 ✅ (berkurang 10)
   - Lily Putih: 20 → 15 ✅ (berkurang 5)
3. Cek "Total Dipakai":
   - Mawar Merah: 0 → 10 ✅
   - Lily Putih: 0 → 5 ✅
```

### Step 8: Test Delete Production (Restore Stock)
```
1. Klik "Daftar Produksi"
2. Klik "Hapus" pada produksi yang baru dibuat
3. Konfirmasi delete
4. Redirect ke Daftar Produksi
5. Klik "Manajemen Inventory"
6. Verifikasi stok kembali:
   - Mawar Merah: 40 → 50 ✅
   - Lily Putih: 15 → 20 ✅
```

---

## ⚠️ ERROR TESTING

### Test: Stok Tidak Cukup
```
1. Buka "Tambah Produksi"
2. Pilih bunga: "Gerbera Pink (30)"
3. Input jumlah: "50" (lebih dari stok 30)
4. Lihat pesan error: "Stok tidak cukup" ❌
5. Klik Simpan
6. HARUS error: "Stok bunga Gerbera Pink tidak cukup! Stok tersedia: 30"
```

### Test: Tidak Ada Bahan
```
1. Buka "Tambah Produksi"
2. Isi informasi produk
3. Langsung klik "Simpan" tanpa pilih bahan
4. HARUS error: "Minimal harus ada 1 bahan untuk produksi!"
```

---

## 🎯 SUCCESS CRITERIA

| Test | Result | Status |
|------|--------|--------|
| Status Kesegaran muncul (Segar/Segera Habis/Kadaluarsa) | ✅ | OK |
| Status Kesegaran berubah saat edit tanggal | ✅ | OK |
| Production created | ✅ | OK |
| Stock berkurang saat production dibuat | ✅ | OK |
| Total_used bertambah | ✅ | OK |
| Success message tampil | ✅ | OK |
| Production deleted | ✅ | OK |
| Stock dikembalikan saat production dihapus | ✅ | OK |
| Error message saat stok kurang | ✅ | OK |

---

## 📝 NOTES

- Semua produksi menggunakan transaction untuk data integrity
- Stok otomatis berkurang saat produksi disimpan
- Stok otomatis dikembalikan saat produksi dihapus
- Status kesegaran update real-time berdasarkan tanggal kadaluarsa
- Validation strict untuk mencegah stok negatif

**SEMUA FITUR SUDAH BERFUNGSI! ✅**
