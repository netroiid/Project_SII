@extends('layouts.app')
@section('title', 'Tambah Bunga Baru')

@section('content')
<div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.1)">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('inventory.store') }}">
            @csrf
            <div class="modal-content rounded-3">
                <div class="border-0 modal-header">
                    <h5 class="modal-title">Tambah Bunga Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Bunga</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Kategori</label>
                        <input type="text" name="kategori" class="form-control" list="kategori-list" required placeholder="Mawar, Tulip, Lily, dll">
                        <datalist id="kategori-list">
                            <option value="Mawar">
                            <option value="Tulip">
                            <option value="Lily">
                            <option value="Anggrek">
                            <option value="Krisan">
                            <option value="Gerbera">
                            <option value="Lainnya">
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Stok</label>
                        <input type="number" name="stock_now" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Kadaluarsa</label>
                        <input type="date" name="expired_at" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga per Unit (Rp)</label>
                        <input type="number" name="price_per_unit" class="form-control" min="0" value="0">
                    </div>
                </div>
                <div class="border-0 modal-footer">
                    <a href="{{ route('inventory.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-pink">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
