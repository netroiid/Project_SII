@extends('layouts.app')
@section('title', 'Tambah Produksi Baru')

@section('content')
<div class="mb-4">
    <h4>Tambah Produksi Baru</h4>
    <p>Catat produk yang dibuat dan alokasi bahan baku dari inventory.</p>
</div>

@if($order)
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="ri-shopping-cart-line me-2"></i>
    <div>
        Mencatat produksi untuk **Pesanan: {{ $order->no_order }}** milik **{{ $order->nama_pelanggan }}**.
    </div>
</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('productions.store') }}" id="form-produk">
            @csrf

            @if($order)
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            @endif

            {{-- INFORMASI PRODUK UTAMA --}}
            <div class="p-4 mb-4 border rounded-3 bg-light">
                <h5 class="mb-3 text-primary">
                    <i class="ri-flower-line me-2"></i> Informasi Produk
                </h5>

                <div class="row g-3">
                    {{-- Kategori Produk --}}
                    <div class="col-md-3">
                        <label for="product-type-select" class="form-label small text-muted">Kategori Produk</label>
                        <select id="product-type-select" class="form-select @error('product_name') is-invalid @enderror"
                            required>
                            <option value="">Pilih Kategori</option>
                            <option value="buket">Buket</option>
                            <option value="rangkaian_meja">Rangkaian Meja</option>
                            <option value="dekorasi">Dekorasi</option>
                            <!-- Tambahkan kategori lain sesuai kebutuhan -->
                        </select>
                        @error('product_name')
                        <div class="invalid-feedback">Pilih kategori yang valid.</div>
                        @enderror
                    </div>

                    {{-- Nama Produk --}}
                    <div class="col-md-5">
                        <label for="product-name-select" class="form-label small text-muted">Nama Produk *</label>
                        <input list="productNameList" type="text" id="product-name-select" name="product_name"
                            class="form-control @error('product_name') is-invalid @enderror"
                            placeholder="Pilih dari template atau ketik custom" value="{{ old('product_name') }}"
                            required>
                        <datalist id="productNameList"></datalist>
                        @error('product_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jumlah Produksi --}}
                    <div class="col-md-2">
                        <label for="quantity" class="form-label small text-muted">Jumlah (pcs) *</label>
                        <input name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}"
                            class="form-control @error('quantity') is-invalid @enderror" required>
                        @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Produksi --}}
                    <div class="col-md-2">
                        <label for="date" class="form-label small text-muted">Tanggal Produksi *</label>
                        <input name="date" type="date" class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Nama Pelanggan --}}
                <div class="mt-3">
                    <label for="customer" class="form-label small text-muted">Nama Pelanggan (Opsional)</label>
                    <input name="customer" class="form-control @error('customer') is-invalid @enderror"
                        value="{{ old('customer', $order ? $order->nama_pelanggan : '') }}"
                        placeholder="Nama pelanggan...">
                    @error('customer')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- PILIH BAHAN BAKU -->
            <div class="p-4 mb-4 border rounded-3">
                <h5 class="mb-3 text-secondary">
                    <i class="ri-flask-line me-2"></i> Bahan Baku Digunakan
                </h5>

                <div id="list-bahan">
                    <!-- Rows bahan baku akan ditambahkan di sini oleh JS -->
                </div>

                <button type="button" class="mt-3 btn btn-outline-secondary btn-sm" id="btn-tambah-bahan">
                    <i class="ri-add-line me-1"></i> Tambah Bahan
                </button>
            </div>

            {{-- CATATAN TAMBAHAN --}}
            <div class="mb-4">
                <label for="note" class="form-label small text-muted">Catatan Tambahan (Opsional)</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
            </div>

            {{-- AKSI --}}
            <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                @if($order)
                <a href="{{ route('pesanan.show', $order->id) }}" class="btn btn-outline-secondary me-2">
                    <i class="ri-close-line"></i> Batal
                </a>
                @else
                <a href="{{ route('productions.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="ri-close-line"></i> Batal
                </a>
                @endif
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Simpan Produksi & Kurangi Stok
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Data Inventory Bunga dan JS Logic --}}
<script>
    // Data dari Controller
    const flowers = @json($flowers);
    const productTemplates = @json($productTemplates);

    // Variabel index untuk array input
    let bahanIndex = 0;

    // Fungsi untuk membuat baris input bahan baku baru
    function bahanBaruRow(idx) {
        return `
            <div class="row mb-2 g-2 align-items-center border-bottom pb-2" data-index="${idx}">
                <div class="col-md-5">
                    <label class="form-label small text-muted">Bunga/Bahan</label>
                    <select name="flowers[${idx}][id]" class="form-select bunga-select flower-id-select" required>
                        <option value="">Pilih bunga dari inventory</option>
                        ${flowers.map(f =>
                            `<option value="${f.id}" data-stock="${f.stock_now}">${f.name} (Stok: ${f.stock_now})</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Jumlah Digunakan</label>
                    <input type="number" name="flowers[${idx}][quantity]" class="form-control jml-bahan" min="1" value="0" required>
                    <div class="invalid-feedback stok-info"></div>
                </div>
                <div class="col-md-2 text-center">
                    <span class="small text-muted d-block">Sisa Stok (Estimasi)</span>
                    <strong class="curr-stock text-success">-</strong>
                </div>
                <div class="col-md-2 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-hapus-bahan" title="Hapus Bahan">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>`;
    }

    // Fungsi untuk memperbarui tampilan stok dan validasi
    function updateStokView(select, jumlahInput) {
        const selectedOption = select.find(':selected');
        const stock = parseInt(selectedOption.data('stock') || 0);
        const value = parseInt(jumlahInput.val() || 0);
        const stockInfo = jumlahInput.siblings('.invalid-feedback');
        const stockDisplay = jumlahInput.closest('.row').find('.curr-stock');

        const remaining = stock - value;

        stockDisplay.text(remaining);

        if (remaining < 0) {
            stockDisplay.removeClass('text-success').addClass('text-danger').text(`Stok Kurang (${remaining})`);
            jumlahInput.addClass('is-invalid');
            stockInfo.text('Kuantitas melebihi stok tersedia!');
        } else {
            stockDisplay.removeClass('text-danger').addClass('text-success');
            jumlahInput.removeClass('is-invalid');
            stockInfo.text('');
        }
    }

    // Logic JQuery
    $(function() {
        // --- 1. PRODUCT TEMPLATES ---
        $('#product-type-select').on('change', function() {
            const category = this.value;
            const productInput = $('#product-name-select');
            const datalist = $('#productNameList');

            // Clear previous options
            datalist.html('');
            productInput.val('');

            if (category && productTemplates[category]) {
                productTemplates[category].forEach(product => {
                    $('<option>').val(product).appendTo(datalist);
                });
            }
        });

        // --- 2. MATERIAL LOGIC ---
        // Tambah baris bahan
        $('#btn-tambah-bahan').click(function() {
            $('#list-bahan').append(bahanBaruRow(bahanIndex++));
        });

        // Hapus baris bahan
        $('#list-bahan').on('click', '.btn-hapus-bahan', function() {
            $(this).closest('.row').remove();
        });

        // Event change pada select bunga
        $('#list-bahan').on('change', '.bunga-select', function() {
            const jumlahInput = $(this).closest('.row').find('.jml-bahan');
            // Reset jumlah input saat bunga diganti
            jumlahInput.val(1);
            updateStokView($(this), jumlahInput);
        });

        // Event input pada jumlah bahan
        $('#list-bahan').on('input', '.jml-bahan', function() {
            const select = $(this).closest('.row').find('.bunga-select');
            updateStokView(select, $(this));
        });

        // Pertama kali muncul, otomatis ada 1 baris bahan
        $('#btn-tambah-bahan').trigger('click');


        // --- 3. FINAL SUBMISSION VALIDATION ---
        $('#form-produk').submit(function(e) {
            let valid = true;

            // Cek apakah ada minimal 1 bahan
            if ($('#list-bahan .row').length === 0) {
                e.preventDefault();
                alert('Minimal harus ada 1 bahan baku yang digunakan!');
                return;
            }

            // Cek validasi stok terakhir
            $('#list-bahan .row').each(function() {
                const jumlah = $(this).find('.jml-bahan');
                if (jumlah.hasClass('is-invalid')) {
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Terdapat bahan baku dengan kuantitas yang melebihi stok tersedia. Harap perbaiki sebelum menyimpan.');
            }
        });
    });
</script>
@endsection
