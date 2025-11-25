@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<div class="mb-4">
    <h4>Laporan & Analisis Inventory</h4>
    <p class="text-muted">Ringkasan stok, nilai aset, dan histori produksi.</p>
</div>

{{-- HEADER & EXPORT --}}
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h5 class="text-secondary">Ringkasan Kinerja</h5>
    <a href="#" class="btn btn-primary">
        <i class="ri-file-download-line me-1"></i> Export Laporan
    </a>
</div>

{{-- 1. SUMMARY CARDS --}}
<div class="row g-4 mb-5">

    {{-- Total Nilai Stok --}}
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-start border-4 border-success">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <i class="ri-wallet-line ri-2x text-success me-3"></i>
                    <div>
                        <small class="text-muted d-block">Nilai Total Stok Inventory</small>
                        <h4 class="mt-1 mb-0 fw-bold text-success">
                            Rp {{ number_format($nilai_total_stok,0,',','.') }}
                        </h4>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">{{ $laporan_stok->count() }} jenis bunga aktif</small>
            </div>
        </div>
    </div>

    {{-- Produksi Bulan Ini --}}
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-start border-4 border-info">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <i class="ri-box-3-line ri-2x text-info me-3"></i>
                    <div>
                        <small class="text-muted d-block">Produksi Bulan Ini</small>
                        <h4 class="mt-1 mb-0 fw-bold text-info">
                            {{ number_format($produksi_bulan_ini, 0, ',', '.') }} item
                        </h4>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">{{ now()->format('F Y') }}</small>
            </div>
        </div>
    </div>

    {{-- Alert Stok --}}
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-start border-4 border-danger">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <i class="ri-alarm-warning-line ri-2x text-danger me-3"></i>
                    <div>
                        <small class="text-muted d-block">Peringatan Stok Rendah/Kadaluarsa</small>
                        <h4 class="mt-1 mb-0 fw-bold text-danger">
                            {{ $alert_stok }} item
                        </h4>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">Perlu perhatian segera</small>
            </div>
        </div>
    </div>
</div>

{{-- 2. LAPORAN STOK DETIL --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0 text-dark"><i class="ri-list-check-2 me-2"></i> Detail Stok Inventory</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr class="table-secondary">
                        <th>Nama Bunga</th>
                        <th>Kategori</th>
                        <th>Stok Saat Ini</th>
                        <th>Nilai Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan_stok as $row)
                    <tr>
                        <td><strong>{{ $row->name }}</strong></td>
                        <td>{{ $row->kategori }}</td>
                        <td>
                            {{ $row->stock_now }} tangkai
                            @if($row->stock_now < 10) <span class="badge bg-warning text-dark ms-1">Menipis</span>
                                @endif
                        </td>
                        <td>Rp {{ number_format($row->nilai_stok,0,',','.') }}</td>
                        <td>
                            @if($row->status === 'Kadaluarsa')
                            <span class="badge bg-danger">Kadaluarsa</span>
                            @elseif($row->status === 'Segera Habis')
                            <span class="badge bg-warning text-dark">Segera Habis</span>
                            @else
                            <span class="badge bg-success">Aman</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Data stok tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 3. LAPORAN PENGGUNAAN PRODUKSI --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0 text-dark"><i class="ri-tools-line me-2"></i> Penggunaan Bahan Baku (Bulan Ini)</h6>
    </div>
    <div class="card-body">
        @if($penggunaan->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="ri-information-line me-1"></i> Belum ada produksi yang dicatat bulan ini.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr class="table-secondary">
                        <th>Nama Bunga</th>
                        <th>Total Digunakan (tangkai)</th>
                        <th>Persentase dari Stok Awal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penggunaan as $p)
                    <tr>
                        <td>{{ $p->flower_name }}</td>
                        <td><strong>{{ $p->total_used }}</strong></td>
                        <td>-</td> {{-- Placeholder: Perhitungan persentase harus dilakukan di Controller --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- 4. PERINGATAN --}}
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h6 class="mb-0"><i class="ri-alert-line me-2"></i> Peringatan & Rekomendasi Aksi</h6>
    </div>
    <div class="card-body">
        @if($alert_stok > 0)
        <div class="alert alert-warning border-warning">
            <i class="ri-error-warning-line me-1"></i> **{{ $alert_stok }} item** membutuhkan perhatian segera:
        </div>
        <ul class="list-group list-group-flush">
            @foreach($laporan_stok->where('stock_now','<',10) as $low) <li
                class="list-group-item d-flex justify-content-between align-items-center">
                {{ $low->name }}
                <span class="badge bg-danger">{{ $low->stock_now }} tangkai</span>
                </li>
                @endforeach
                {{-- Tambahkan peringatan kadaluarsa di sini jika ada --}}
        </ul>
        @else
        <div class="text-muted">Tidak ada peringatan stok atau kadaluarsa saat ini. Inventory dalam kondisi baik.</div>
        @endif
    </div>
</div>
@endsection
