@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h4>Edit Produksi</h4>
    <p>Perbarui detail catatan produksi.</p>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('productions.update', $production->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Menggunakan method PUT untuk pembaruan --}}

            <div class="row">
                {{-- Kolom Kiri: Detail Produksi --}}
                <div class="col-md-6">

                    {{-- Tanggal --}}
                    <div class="mb-3">
                        <label for="date" class="form-label">Tanggal Produksi</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                            name="date" value="{{ old('date', $production->date) }}" required>
                        @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Produk --}}
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Nama Produk</label>
                        {{-- Idealnya, ini adalah dropdown yang memilih dari daftar produk --}}
                        <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                            id="product_name" name="product_name"
                            value="{{ old('product_name', $production->product_name) }}" required>
                        @error('product_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jenis --}}
                    <div class="mb-3">
                        <label for="type" class="form-label">Jenis / Varian</label>
                        <input type="text" class="form-control @error('type') is-invalid @enderror" id="type"
                            name="type" value="{{ old('type', $production->type) }}">
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah Produksi (pcs)</label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity"
                            name="quantity" value="{{ old('quantity', $production->quantity) }}" min="1" required>
                        @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pelanggan (Jika produksi untuk pesanan tertentu) --}}
                    <div class="mb-3">
                        <label for="customer" class="form-label">Pelanggan (Opsional)</label>
                        <input type="text" class="form-control @error('customer') is-invalid @enderror" id="customer"
                            name="customer" value="{{ old('customer', $production->customer) }}"
                            placeholder="Nama pelanggan/pesanan terkait">
                        @error('customer')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- Kolom Kanan: Bahan Digunakan --}}
                <div class="col-md-6">
                    <h6>Bahan Baku Digunakan</h6>
                    <p class="text-muted small">Perbarui jumlah bahan yang digunakan untuk produksi ini.</p>

                    <div id="materials-container">
                        @php
                        // Ambil data bahan yang sudah terhubung (dari relasi pivot)
                        $used_flowers = $production->flowers->keyBy('id');
                        @endphp

                        @foreach($allFlowers as $flower) {{-- Asumsi $allFlowers adalah daftar semua bahan baku --}}
                        <div class="mb-3">
                            <label for="material-{{ $flower->id }}" class="form-label">{{ $flower->name }}</label>
                            @php
                            // Tentukan kuantitas yang sudah ada, atau 0 jika belum terpakai
                            $quantity_used = $used_flowers->has($flower->id) ?
                            $used_flowers[$flower->id]->pivot->quantity_used : 0;
                            @endphp
                            <input type="number" class="form-control" name="materials[{{ $flower->id }}]"
                                id="material-{{ $flower->id }}"
                                value="{{ old('materials.' . $flower->id, $quantity_used) }}" min="0">
                        </div>
                        @endforeach

                        {{-- Catatan: Agar kode ini berfungsi, Anda perlu mengirimkan variabel $allFlowers dari
                        controller --}}
                        @if(!isset($allFlowers) || $allFlowers->isEmpty())
                        <p class="text-info">Tidak ada bahan baku tersedia. Tambahkan bahan baku terlebih dahulu.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                <a href="{{ route('productions.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
