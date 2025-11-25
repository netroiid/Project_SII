@extends('layouts.app')
@section('title', 'Buat Pesanan')

@section('content')
<div class="mb-4">
    <h4>Buat Pesanan Baru</h4>
    <p>Masukkan data pelanggan, detail pengiriman, dan item pesanan baru.</p>
</div>

<form action="{{ route('pesanan.store') }}" method="POST" id="orderForm">
    @csrf

    {{-- 1. DATA PELANGGAN & PENGIRIMAN --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="ri-user-line me-2"></i> Data Pelanggan & Pengiriman</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Nama Pelanggan *</label>
                    <input type="text" name="nama_pelanggan"
                        class="form-control @error('nama_pelanggan') is-invalid @enderror"
                        value="{{ old('nama_pelanggan') }}" required>
                    @error('nama_pelanggan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">No. Telepon *</label>
                    <input type="tel" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror"
                        placeholder="08xx-xxxx-xxxx" value="{{ old('no_telepon') }}" required>
                    @error('no_telepon')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label small text-muted">Alamat Pengiriman *</label>
                <textarea name="alamat_pengiriman" class="form-control @error('alamat_pengiriman') is-invalid @enderror"
                    rows="3" required>{{ old('alamat_pengiriman') }}</textarea>
                @error('alamat_pengiriman')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- 2. DETAIL PESANAN UTAMA --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h6 class="mb-0"><i class="ri-calendar-line me-2"></i> Jadwal Pesanan</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">No. Pesanan *</label>
                    <input type="text" name="no_order" class="form-control @error('no_order') is-invalid @enderror"
                        value="{{ $nextNo }}" required readonly>
                    @error('no_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Tanggal Pesan *</label>
                    <input type="date" name="tanggal_pesan"
                        class="form-control @error('tanggal_pesan') is-invalid @enderror"
                        value="{{ old('tanggal_pesan', now()->format('Y-m-d')) }}" required>
                    @error('tanggal_pesan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Tanggal Kirim *</label>
                    <input type="date" name="tanggal_kirim"
                        class="form-control @error('tanggal_kirim') is-invalid @enderror"
                        value="{{ old('tanggal_kirim') }}" required>
                    @error('tanggal_kirim')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- 3. ITEM PESANAN --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="ri-shopping-bag-line me-2"></i> Item Pesanan</h6>
        </div>
        <div class="card-body">
            <div id="itemsContainer">
                {{-- Item Row Default --}}
                <div class="item-row mb-3 p-3 border rounded">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Kategori Produk *</label>
                            <select class="form-select category-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="buket">Buket</option>
                                <option value="rangkaian_meja">Rangkaian Meja</option>
                                <option value="dekorasi">Dekorasi</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Nama Produk *</label>
                            <input list="productList0" type="text" name="items[0][nama_produk]"
                                class="form-control product-select" placeholder="Pilih atau ketik produk" required>
                            <datalist id="productList0" class="product-datalist"></datalist>
                            <input type="hidden" name="items[0][product_category]" class="product-category-hidden">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Jumlah *</label>
                            <input type="number" name="items[0][jumlah]" class="form-control quantity" min="1" value="1"
                                required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Harga/Unit (Rp) *</label>
                            <input type="number" name="items[0][harga_satuan]" class="form-control unit-price" min="0"
                                step="1000" value="0" required>
                        </div>
                        <div class="col-md-2 d-flex flex-column align-items-end">
                            <label class="form-label small text-muted w-100">Subtotal</label>
                            <strong class="text-primary w-100" id="subtotal0">Rp 0</strong>
                            <input type="hidden" class="subtotal-hidden" value="0">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label small text-muted">Spesifikasi Khusus</label>
                            <input type="text" name="items[0][spesifikasi]" class="form-control"
                                placeholder="Warna, jenis bunga, catatan kartu ucapan...">
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                            <i class="ri-delete-bin-line me-1"></i> Hapus Item
                        </button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-success mt-3" id="addItem">
                <i class="ri-add-line me-1"></i> Tambah Item
            </button>
        </div>
    </div>

    {{-- 4. TOTAL HARGA & PEMBAYARAN --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 text-dark"><i class="ri-calculator-line me-2"></i> Ringkasan & Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <h5 class="text-dark">Total Harga Pesanan: <span id="totalPrice" class="text-primary">Rp 0</span>
                    </h5>
                </div>
            </div>

            <div class="row g-3 mt-3 border-top pt-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Metode Pembayaran *</label>
                    <select name="metode_pembayaran"
                        class="form-select @error('metode_pembayaran') is-invalid @enderror" required>
                        <option value="">Pilih Metode</option>
                        <option value="transfer" {{ old('metode_pembayaran')==='transfer' ? 'selected' : '' }}>Transfer
                        </option>
                        <option value="tunai" {{ old('metode_pembayaran')==='tunai' ? 'selected' : '' }}>Tunai</option>
                    </select>
                    @error('metode_pembayaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Status Pembayaran *</label>
                    <select name="status_pembayaran"
                        class="form-select @error('status_pembayaran') is-invalid @enderror" required>
                        <option value="">Pilih Status</option>
                        <option value="belum_bayar" {{ old('status_pembayaran')==='belum_bayar' ? 'selected' : '' }}>
                            Belum Bayar</option>
                        <option value="sudah_bayar" {{ old('status_pembayaran')==='sudah_bayar' ? 'selected' : '' }}>
                            Sudah Bayar</option>
                    </select>
                    @error('status_pembayaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Catatan Pesanan</label>
                <textarea name="catatan" class="form-control" rows="3"
                    placeholder="Catatan tambahan untuk pesanan ini...">{{ old('catatan') }}</textarea>
            </div>
        </div>
    </div>

    {{-- BUTTON AKSI --}}
    <div class="mt-4 pt-3 border-top d-flex justify-content-end">
        <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary me-2">
            <i class="ri-close-line me-1"></i> Batal
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line me-1"></i> Simpan Pesanan
        </button>
    </div>
</form>

<script>
    const productTemplates = @json($productTemplates);
    let itemIndex = 1;

    function formatRupiah(number) {
        return 'Rp ' + (number || 0).toLocaleString('id-ID');
    }

    // Fungsi untuk membuat baris item baru
    function createNewItemRow(index) {
        return `
            <div class="item-row mb-3 p-3 border rounded">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Kategori Produk *</label>
                        <select class="form-select category-select" required>
                            <option value="">Pilih Kategori</option>
                            <option value="buket">Buket</option>
                            <option value="rangkaian_meja">Rangkaian Meja</option>
                            <option value="dekorasi">Dekorasi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Nama Produk *</label>
                        <input list="productList${index}" type="text" name="items[${index}][nama_produk]" class="form-control product-select" placeholder="Pilih atau ketik produk" required>
                        <datalist id="productList${index}" class="product-datalist"></datalist>
                        <input type="hidden" name="items[${index}][product_category]" class="product-category-hidden">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Jumlah *</label>
                        <input type="number" name="items[${index}][jumlah]" class="form-control quantity" min="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Harga/Unit (Rp) *</label>
                        <input type="number" name="items[${index}][harga_satuan]" class="form-control unit-price" min="0" step="1000" value="0" required>
                    </div>
                    <div class="col-md-2 d-flex flex-column align-items-end">
                        <label class="form-label small text-muted w-100">Subtotal</label>
                        <strong class="text-primary w-100" id="subtotal${index}">Rp 0</strong>
                        <input type="hidden" class="subtotal-hidden" value="0">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label class="form-label small text-muted">Spesifikasi Khusus</label>
                        <input type="text" name="items[${index}][spesifikasi]" class="form-control" placeholder="Warna, jenis bunga, catatan kartu ucapan...">
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                        <i class="ri-delete-bin-line me-1"></i> Hapus Item
                    </button>
                </div>
            </div>
        `;
    }

    // Hitung total harga
    function updateTotalPrice() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const subtotalInput = row.querySelector('.subtotal-hidden');
            total += parseInt(subtotalInput.value) || 0;
        });
        document.getElementById('totalPrice').textContent = formatRupiah(total);
    }

    // Hitung subtotal per baris
    function calculateSubtotal(itemRow, index) {
        const quantity = parseInt(itemRow.querySelector('.quantity').value) || 0;
        const price = parseInt(itemRow.querySelector('.unit-price').value) || 0;
        const subtotal = quantity * price;

        itemRow.querySelector(`#subtotal${index}`).textContent = formatRupiah(subtotal);
        itemRow.querySelector('.subtotal-hidden').value = subtotal;
        updateTotalPrice();
    }

    // --- EVENT LISTENERS ---

    document.addEventListener('change', function(e) {
        // 1. Handle category change & product template update
        if (e.target.classList.contains('category-select')) {
            const category = e.target.value;
            const itemRow = e.target.closest('.item-row');
            const productInput = itemRow.querySelector('.product-select');
            const categoryHidden = itemRow.querySelector('.product-category-hidden');

            const datalistId = productInput.getAttribute('list');
            const datalist = document.getElementById(datalistId);

            datalist.innerHTML = '';
            productInput.value = '';

            if (category && productTemplates[category]) {
                productTemplates[category].forEach(product => {
                    const option = document.createElement('option');
                    option.value = product;
                    datalist.appendChild(option);
                });
            }
            categoryHidden.value = category;
        }

        // 2. Calculate subtotal on quantity/price change
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            const itemRow = e.target.closest('.item-row');
            const indexMatch = itemRow.querySelector('.product-select').name.match(/items\[(\d+)\]/);
            const index = indexMatch ? parseInt(indexMatch[1]) : 0;
            calculateSubtotal(itemRow, index);
        }
    });

    // Add new item row
    document.getElementById('addItem').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const newRowHTML = createNewItemRow(itemIndex);
        container.insertAdjacentHTML('beforeend', newRowHTML);
        itemIndex++;
        updateTotalPrice(); // Update total price for consistency
    });

    // Remove item row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const itemRows = document.querySelectorAll('.item-row');
            if (itemRows.length > 1) {
                e.target.closest('.item-row').remove();
                updateTotalPrice();
            } else {
                // Mengganti alert() dengan tampilan pesan yang lebih baik jika ini adalah aplikasi production
                console.error('Minimal harus ada 1 item pesanan!');
                alert('Minimal harus ada 1 item pesanan!');
            }
        }
    });

    // Initial calculation (karena item index 0 sudah ada)
    document.addEventListener('DOMContentLoaded', () => {
        calculateSubtotal(document.querySelector('#itemsContainer .item-row'), 0);
    });
</script>
@endsection
