@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h4>Dashboard Utama</h4>
    <p class="text-muted">Ringkasan cepat kinerja usaha bunga segar Anda.</p>
</div>

{{-- 1. SUMMARY CARDS (Menggunakan style Laporan) --}}
<div class="row g-4 mb-5">

    {{-- Total Stok Bunga --}}
    <div class="col-md-3">
        <div class="card shadow-sm h-100 border-start border-4 border-success">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <i class="ri-leaf-line ri-2x text-success me-3"></i>
                    <div>
                        <small class="text-muted d-block">Total Stok Inventory</small>
                        <h4 class="mt-1 mb-0 fw-bold text-success">
                            {{ number_format($total_stok, 0, ',', '.') }} <small class="text-muted">tangkai</small>
                        </h4>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">{{ $jenis_bunga }} jenis bunga tersedia</small>
            </div>
        </div>
    </div>

    {{-- Total Pesanan --}}
    <div class="col-md-3">
        <div class="card shadow-sm h-100 border-start border-4 border-info">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <i class="ri-truck-line ri-2x text-info me-3"></i>
                    <div>
                        <small class="text-muted d-block">Total Pesanan</small>
                        <h4 class="mt-1 mb-0 fw-bold text-info">
                            {{ number_format($total_pesanan, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">
                    @if(isset($pesanan_per_status['selesai']))
                    <span class="text-success">{{ $pesanan_per_status['selesai'] }} Selesai</span>
                    @endif
                    @if(isset($pesanan_per_status['proses']))
                    | <span class="text-info">{{ $pesanan_per_status['proses'] }} Diproses</span>
                    @endif
                </small>
            </div>
        </div>
    </div>

    {{-- Total Produksi --}}
    <div class="col-md-3">
        <div class="card shadow-sm h-100 border-start border-4 border-primary">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <i class="ri-tools-line ri-2x text-primary me-3"></i>
                    <div>
                        <small class="text-muted d-block">Total Produk Dibuat</small>
                        <h4 class="mt-1 mb-0 fw-bold text-primary">
                            {{ number_format($total_produksi, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">Produk yang telah dibuat</small>
            </div>
        </div>
    </div>

    {{-- Status Alert Stok --}}
    <div class="col-md-3">
        <div class="card shadow-sm h-100 border-start border-4 border-danger">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <i class="ri-alert-line ri-2x text-danger me-3"></i>
                    <div>
                        <small class="text-muted d-block">Status Peringatan</small>
                        <h4 class="mt-1 mb-0 fw-bold text-danger">
                            {{ $stok_menipis }} Menipis
                        </h4>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">{{ $kadaluarsa }} segera kadaluarsa</small>
            </div>
        </div>
    </div>
</div>

{{-- 2. DETAIL LISTS --}}
<div class="row g-4">

    {{-- KOLOM KIRI --}}
    <div class="col-md-6">

        {{-- Produk Paling Laris --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-dark"><i class="ri-heart-line me-2"></i> Produk Paling Laris</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($top_products->take(3) as $p)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $p->nama_produk }}</strong>
                            <div class="text-muted small">{{ $p->product_category ?? 'Produk' }}</div>
                        </div>
                        <span class="badge bg-primary text-white p-2">{{ $p->total }}x dipesan</span>
                    </li>
                    @empty
                    <li class="list-group-item text-muted text-center py-3">Belum ada pesanan yang tercatat.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Stok Menipis (Alert) --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="ri-emotion-unhappy-line me-2"></i> Stok Bunga Menipis</h6>
            </div>
            <div class="card-body p-0">
                @if($low_stock_flowers->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($low_stock_flowers->take(5) as $flower)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $flower->name }}</strong>
                            <div class="small text-muted">Kategori: {{ $flower->kategori }}</div>
                        </div>
                        <div>
                            <span class="badge bg-danger p-2">Sisa: {{ $flower->stock_now }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center text-muted py-4">Semua stok bunga di atas batas aman.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="col-md-6">

        {{-- Bunga Segera Kadaluarsa --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="ri-time-line me-2"></i> Segera Kadaluarsa</h6>
            </div>
            <div class="card-body p-0">
                @if($expiring_flowers->isEmpty())
                <div class="text-center text-muted py-4">Tidak ada bunga yang segera kadaluarsa dalam 3 hari ke depan.
                </div>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($expiring_flowers as $f)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>{{ $f->name }}</strong>
                        <span class="text-danger small">
                            Kadaluarsa {{ \Carbon\Carbon::parse($f->expired_at)->diffForHumans() }} ({{
                            \Carbon\Carbon::parse($f->expired_at)->format('d/m/Y') }})
                        </span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        {{-- Pesanan Terbaru --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-dark"><i class="ri-list-ordered me-2"></i> 5 Pesanan Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($latest_orders as $ord)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $ord->no_order }}</strong>
                            <div class="small text-muted">{{ $ord->nama_pelanggan }} | Kirim: {{
                                \Carbon\Carbon::parse($ord->tanggal_kirim)->format('d/m/Y') }}</div>
                        </div>
                        @php
                        $statusColors =
                        ['pending'=>'secondary','proses'=>'info','dikirim'=>'warning','selesai'=>'success'];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$ord->status_pesanan] ?? 'secondary' }} p-2">{{
                            ucfirst($ord->status_pesanan) }}</span>
                    </li>
                    @empty
                    <li class="list-group-item text-muted text-center py-3">Belum ada pesanan yang tercatat.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- 3. SUMMARY FOOTER (Dihapus karena sudah ada di Summary Cards) --}}
{{-- Bagian Laporan Singkat di bagian bawah dihilangkan karena redundan dengan Summary Cards di atas. --}}

@endsection
