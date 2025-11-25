@extends('layouts.app')
@section('title', 'Pesanan')

@section('content')
<h4>Manajemen Pesanan</h4>
<p>Kelola pesanan dan pelanggan</p>

<div class="mt-3 card">
    <div class="card-body">

        {{-- HEADER --}}
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h6>Daftar Pesanan</h6>
            <a href="{{ route('pesanan.create') }}" class="btn btn-primary btn-sm">
                + Buat Pesanan
            </a>
        </div>

        {{-- SEARCH --}}
        <input type="text" class="mb-3 form-control" placeholder="Cari pesanan atau pelanggan...">

        {{-- TABLE --}}
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>Pelanggan</th>
                    <th>Tanggal Kirim</th>
                    <th>Total Item</th>
                    <th>Total Harga</th>
                    <th>Metode Bayar</th>
                    <th>Status Bayar</th>
                    <th>Status Order</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><strong>{{ $order->no_order }}</strong></td>
                    <td>
                        {{ $order->nama_pelanggan }}<br>
                        <small class="text-muted">{{ $order->no_telepon }}</small>
                    </td>
                    <td>{{ $order->tanggal_kirim->format('d/m/Y') }}</td>
                    <td>{{ $order->items->count() }} item</td>
                    <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                    <td>
                        @if($order->metode_pembayaran === 'transfer')
                            <span class="badge bg-info">Transfer</span>
                        @else
                            <span class="badge bg-warning">Tunai</span>
                        @endif
                    </td>
                    <td>
                        @if($order->status_pembayaran === 'sudah_bayar')
                            <span class="badge bg-success">Sudah Bayar</span>
                        @else
                            <span class="badge bg-danger">Belum Bayar</span>
                        @endif
                    </td>
                    <td>
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
                        <span class="badge bg-{{ $statusColors[$order->status_pesanan] ?? 'secondary' }}">
                            {{ $statusLabels[$order->status_pesanan] ?? 'Unknown' }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('pesanan.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                            <i class="ri-eye-line"></i>
                        </a>
                        <a href="{{ route('pesanan.edit', $order->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                            <i class="ri-pencil-line"></i>
                        </a>
                        <form action="{{ route('pesanan.destroy', $order->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus pesanan ini?')" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        Belum ada pesanan. <a href="{{ route('pesanan.create') }}">Buat pesanan baru</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
