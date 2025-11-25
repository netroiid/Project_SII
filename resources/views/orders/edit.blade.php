@extends('layouts.app')
@section('title', 'Edit Pesanan ' . $pesanan->no_order)

@section('content')
<div class="container-fluid">
    <h4>Edit Pesanan {{ $pesanan->no_order }}</h4>
    <p>Ubah data pesanan dan item.</p>

    <form action="{{ route('pesanan.update', $pesanan->id) }}" method="POST" id="orderForm">
        @csrf
        @method('PUT')

        <div class="mt-3 card">
            <div class="card-body">
                <h6>Data Pelanggan</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Pelanggan *</label>
                        <input type="text" name="nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror"
                            value="{{ old('nama_pelanggan', $pesanan->nama_pelanggan) }}" required>
                        @error('nama_pelanggan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Telepon *</label>
                        <input type="tel" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror"
                            placeholder="08xx-xxxx-xxxx" value="{{ old('no_telepon', $pesanan->no_telepon) }}" required>
                        @error('no_telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Pengiriman *</label>
                    <textarea name="alamat_pengiriman" class="form-control @error('alamat_pengiriman') is-invalid @enderror"
                        rows="3" required>{{ old('alamat_pengiriman', $pesanan->alamat_pengiriman) }}</textarea>
                    @error('alamat_pengiriman')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-3 card">
            <div class="card-body">
                <h6>Detail Pesanan</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">No. Pesanan</label>
                        <input type="text" class="form-control" value="{{ $pesanan->no_order }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Pesan *</label>
                        <input type="date" name="tanggal_pesan" class="form-control @error('tanggal_pesan') is-invalid @enderror"
                            value="{{ old('tanggal_pesan', $pesanan->tanggal_pesan->format('Y-m-d')) }}" required>
                        @error('tanggal_pesan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Kirim *</label>
                        <input type="date" name="tanggal_kirim" class="form-control @error('tanggal_kirim') is-invalid @enderror"
                            value="{{ old('tanggal_kirim', $pesanan->tanggal_kirim->format('Y-m-d')) }}" required>
                        @error('tanggal_kirim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 card">
            <div class="card-body">
                <h6>Item Pesanan</h6>
                <div id="itemsContainer">
                    @foreach($pesanan->items as $index => $item)
                    <div class="item-row mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Jenis Produk *</label>
                                <select name="items[{{ $index }}][production_id]" class="form-control production-select">
                                    <option value="">Pilih Produk Jadi</option>
                                    @foreach($productions as $prod)
                                        <option value="{{ $prod->id }}" data-name="{{ $prod->product_name }}"
                                            {{ $item->production_id === $prod->id ? 'selected' : '' }}>
                                            {{ $prod->product_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nama Produk *</label>
                                <input type="text" name="items[{{ $index }}][nama_produk]" class="form-control product-name"
                                    value="{{ $item->nama_produk }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Jumlah *</label>
                                <input type="number" name="items[{{ $index }}][jumlah]" class="form-control quantity"
                                    min="1" value="{{ $item->jumlah }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Harga/Unit (Rp) *</label>
                                <input type="number" name="items[{{ $index }}][harga_satuan]" class="form-control unit-price"
                                    min="0" step="1000" value="{{ $item->harga_satuan }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control subtotal" readonly
                                    value="Rp {{ number_format($item->subtotal, 0, ',', '.') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Spesifikasi Khusus</label>
                                <input type="text" name="items[{{ $index }}][spesifikasi]" class="form-control"
                                    value="{{ $item->spesifikasi ?? '' }}">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-item">Hapus Item</button>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-success" id="addItem">+ Tambah Item</button>
            </div>
        </div>

        <div class="mt-3 card">
            <div class="card-body">
                <h6>Total Harga</h6>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Total: <span id="totalPrice">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 card">
            <div class="card-body">
                <h6>Pembayaran & Status</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Metode Pembayaran *</label>
                        <select name="metode_pembayaran" class="form-control @error('metode_pembayaran') is-invalid @enderror" required>
                            <option value="transfer" {{ $pesanan->metode_pembayaran === 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="tunai" {{ $pesanan->metode_pembayaran === 'tunai' ? 'selected' : '' }}>Tunai</option>
                        </select>
                        @error('metode_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status Pembayaran *</label>
                        <select name="status_pembayaran" class="form-control @error('status_pembayaran') is-invalid @enderror" required>
                            <option value="belum_bayar" {{ $pesanan->status_pembayaran === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="sudah_bayar" {{ $pesanan->status_pembayaran === 'sudah_bayar' ? 'selected' : '' }}>Sudah Bayar</option>
                        </select>
                        @error('status_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status Pesanan *</label>
                        <select name="status_pesanan" class="form-control @error('status_pesanan') is-invalid @enderror" required>
                            <option value="pending" {{ $pesanan->status_pesanan === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="proses" {{ $pesanan->status_pesanan === 'proses' ? 'selected' : '' }}>Proses</option>
                            <option value="dikirim" {{ $pesanan->status_pesanan === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="selesai" {{ $pesanan->status_pesanan === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        @error('status_pesanan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Pesanan</label>
                    <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $pesanan->catatan) }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('pesanan.show', $pesanan->id) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
    let itemIndex = {{ count($pesanan->items) }};

    // Auto-populate product name from production dropdown
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('production-select')) {
            const selected = e.target.options[e.target.selectedIndex];
            const productName = e.target.closest('.item-row').querySelector('.product-name');
            if (selected.value) {
                productName.value = selected.dataset.name;
            }
        }

        // Calculate subtotal
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            const itemRow = e.target.closest('.item-row');
            const quantity = parseInt(itemRow.querySelector('.quantity').value) || 0;
            const price = parseInt(itemRow.querySelector('.unit-price').value) || 0;
            const subtotal = quantity * price;
            itemRow.querySelector('.subtotal').value = 'Rp ' + subtotal.toLocaleString('id-ID');
            updateTotalPrice();
        }
    });

    // Add new item row
    document.getElementById('addItem').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const newRow = `
            <div class="item-row mb-3 p-3 border rounded">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Jenis Produk *</label>
                        <select name="items[${itemIndex}][production_id]" class="form-control production-select">
                            <option value="">Pilih Produk Jadi</option>
                            @foreach($productions as $prod)
                                <option value="{{ $prod->id }}" data-name="{{ $prod->product_name }}">
                                    {{ $prod->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Nama Produk *</label>
                        <input type="text" name="items[${itemIndex}][nama_produk]" class="form-control product-name" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Jumlah *</label>
                        <input type="number" name="items[${itemIndex}][jumlah]" class="form-control quantity"
                            min="1" value="1" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Harga/Unit (Rp) *</label>
                        <input type="number" name="items[${itemIndex}][harga_satuan]" class="form-control unit-price"
                            min="0" step="1000" value="0" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control subtotal" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Spesifikasi Khusus</label>
                        <input type="text" name="items[${itemIndex}][spesifikasi]" class="form-control">
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-item">Hapus Item</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRow);
        itemIndex++;
    });

    // Remove item row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            const itemRows = document.querySelectorAll('.item-row');
            if (itemRows.length > 1) {
                e.target.closest('.item-row').remove();
                updateTotalPrice();
            } else {
                alert('Minimal harus ada 1 item pesanan!');
            }
        }
    });

    // Calculate total price
    function updateTotalPrice() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseInt(row.querySelector('.quantity').value) || 0;
            const price = parseInt(row.querySelector('.unit-price').value) || 0;
            total += quantity * price;
        });
        document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Initial calculation
    updateTotalPrice();
</script>
@endsection


