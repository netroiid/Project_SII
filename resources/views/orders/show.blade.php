@extends('layouts.app')
@section('title', 'Detail Pesanan ' . $pesanan->no_order)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>{{ $pesanan->no_order }}</h4>
            <p class="text-muted">Detail Pesanan</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('productions.create', ['order_id' => $pesanan->id]) }}" class="btn btn-success">
                <i class="ri-file-add-line"></i> Buat Produksi
            </a>
            <a href="{{ route('pesanan.edit', $pesanan->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('pesanan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Data Pelanggan</h6>
                    <p><strong>Nama:</strong> {{ $pesanan->nama_pelanggan }}</p>
                    <p><strong>No. Telepon:</strong> {{ $pesanan->no_telepon }}</p>
                    <p><strong>Alamat:</strong> {{ $pesanan->alamat_pengiriman }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Detail Pesanan</h6>
                    <p><strong>Tanggal Pesan:</strong> {{ $pesanan->tanggal_pesan->format('d/m/Y') }}</p>
                    <p><strong>Tanggal Kirim:</strong> {{ $pesanan->tanggal_kirim->format('d/m/Y') }}</p>
                    <p>
                        <strong>Metode Pembayaran:</strong>
                        @if($pesanan->metode_pembayaran === 'transfer')
                            <span class="badge bg-info">Transfer</span>
                        @else
                            <span class="badge bg-warning">Tunai</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-title">Item Pesanan</h6>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Harga/Unit</th>
                        <th>Subtotal</th>
                        <th>Spesifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan->items as $item)
                    <tr>
                        <td>{{ $item->nama_produk }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                        <td>{{ $item->spesifikasi ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Status & Pembayaran</h6>
                    <p>
                        <strong>Status Pesanan:</strong>
                        @php
                            $statusColors = [
                                'pending' => 'secondary',
                                'proses' => 'info',
                                'dikirim' => 'warning',
                                'selesai' => 'success',
                            ];
                            $statusLabels = [
                                'pending' => 'Pending',
                                'proses' => 'Proses',
                                'dikirim' => 'Dikirim',
                                'selesai' => 'Selesai',
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$pesanan->status_pesanan] ?? 'secondary' }}">
                            {{ $statusLabels[$pesanan->status_pesanan] ?? 'Unknown' }}
                        </span>
                    </p>
                    <p>
                        <strong>Status Pembayaran:</strong>
                        @if($pesanan->status_pembayaran === 'sudah_bayar')
                            <span class="badge bg-success">Sudah Bayar</span>
                        @else
                            <span class="badge bg-danger">Belum Bayar</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Total Harga</h6>
                    <h4 class="text-primary">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</h4>
                    @if($pesanan->catatan)
                    <p class="mt-2">
                        <strong>Catatan:</strong> {{ $pesanan->catatan }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <form action="{{ route('pesanan.destroy', $pesanan->id) }}" method="POST" style="display:inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus pesanan ini?')">
                Hapus Pesanan
            </button>
        </form>
    </div>
</div>
@endsection
