@extends('layouts.app')
@section('title', 'Inventory')

@section('content')
<h4>Manajemen Inventory</h4>
<p>Kelola stok bunga segar Anda</p>

<div class="alert alert-success d-flex align-items-center" role="alert">
    <!-- Icon Info -->
    <i class="ri-information-line me-2"></i>
    <div>
        Inventory adalah sumber bahan: stok bunga di sini akan
        <b>otomatis berkurang</b> setiap kali Anda membuat produksi.
    </div>
</div>

<div class="mt-3 card">
    <div class="card-body">

        {{-- HEADER --}}
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h6>Daftar Bunga / Inventory</h6>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
                + Tambah Bunga
            </a>
        </div>

        {{-- SEARCH --}}
        <input type="text" class="mb-3 form-control" placeholder="Cari bunga...">

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <thead>
                    <tr>
                        <th class="text-nowrap">Nama Bunga</th>
                        <th class="text-nowrap">Kategori</th>
                        <th class="text-nowrap">Stok Saat Ini</th>
                        <th class="text-nowrap">Total Dipakai</th>
                        <th class="text-nowrap">Tanggal Kadaluarsa</th>
                        <th class="text-nowrap">Status Kesegaran</th>
                        <th class="text-nowrap">Harga/Unit</th>
                        <th class="text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flowers as $f)
                    <tr>
                        <td><strong>{{ $f->name }}</strong></td>
                        <td>{{ $f->kategori }}</td>
                        <td>
                            <span class="d-inline-block me-1">{{ $f->stock_now }}</span>
                            @if($f->stock_now < 10 && $f->stock_now > 0)
                                <span class="badge bg-warning text-dark">Menipis</span>
                                @elseif($f->stock_now <= 0) <span class="badge bg-danger">Habis</span>
                                    @endif
                        </td>
                        <td>{{ $f->total_used ?? 0 }}</td>
                        <td>{{ $f->expired_at ? \Carbon\Carbon::parse($f->expired_at)->format('d/m/Y') : '-' }}</td>
                        <td>
                            @php
                            $status = 'tidak_tahu';
                            $badge_class = 'bg-secondary';
                            $label = '-';

                            if ($f->expired_at) {
                            $expiredDate = \Carbon\Carbon::parse($f->expired_at)->startOfDay();
                            $today = \Carbon\Carbon::today();
                            $daysRemaining = $today->diffInDays($expiredDate, false);

                            if ($daysRemaining < 0) { $status='kadaluarsa' ; $badge_class='bg-danger' ;
                                $label='Kadaluarsa' ; } elseif ($daysRemaining <=3) { $status='segera_habis' ;
                                $badge_class='bg-warning text-dark' ; $label='Segera Habis' ; } else { $status='segar' ;
                                $badge_class='bg-success' ; $label='Segar' ; } } @endphp <span
                                class="badge {{ $badge_class }}">{{ $label }}</span>
                        </td>
                        <td>Rp {{ number_format($f->price_per_unit, 0, ',', '.') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('inventory.edit', $f->id) }}" class="btn btn-sm btn-outline-secondary"
                                title="Edit">
                                <i class="ri-pencil-line"></i>
                            </a>
                            <form action="{{ route('inventory.destroy', $f->id) }}" method="POST"
                                style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus bunga ini?')"
                                    class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Inventory kosong. <a href="{{ route('inventory.create') }}">Tambah bunga baru</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION - Tambahkan jika ada pagination --}}
        {{-- <div class="mt-3">
            {{ $flowers->links() }}
        </div> --}}

    </div>
</div>
@endsection
