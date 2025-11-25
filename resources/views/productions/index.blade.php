@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h4>Daftar Produksi</h4>
    <p>Lihat dan kelola catatan produksi.</p>
</div>

<div class="mt-3 card">
    <div class="card-body">

        {{-- HEADER --}}
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h6>Catatan Produksi</h6>
            <a href="{{ route('productions.create') }}" class="btn btn-primary btn-sm">
                + Tambah Produksi
            </a>
        </div>

        {{-- SEARCH - Opsional, ditambahkan untuk konsistensi dengan contoh pertama --}}
        <input type="text" class="mb-3 form-control" placeholder="Cari produksi, produk, atau pelanggan...">

        {{-- TABLE --}}
        <div class="table-responsive"> {{-- Tambahkan agar tabel responsif di layar kecil --}}
            <table class="table table-bordered table-sm table-hover">
                <thead>
                    <tr>
                        <th class="text-nowrap">Tanggal</th>
                        <th class="text-nowrap">Produk</th>
                        <th class="text-nowrap">Jenis</th>
                        <th class="text-nowrap">Jumlah</th>
                        <th class="text-nowrap">Pelanggan</th>
                        <th class="text-nowrap">Bahan Digunakan</th>
                        <th class="text-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($productions as $production)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($production->date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $production->product_name }}</strong></td>
                        <td>{{ $production->type }}</td>
                        <td>{{ $production->quantity }} pcs</td>
                        <td>{{ $production->customer ?? '-' }}</td>

                        {{-- Bahan digunakan dari relasi pivot --}}
                        <td>
                            <ul class="list-unstyled mb-0 small">
                                @foreach($production->flowers as $flower)
                                <li>
                                    {{ $flower->name }}
                                    <span class="text-muted">({{ $flower->pivot->quantity_used }})</span>
                                </li>
                                @endforeach
                            </ul>
                        </td>

                        <td class="text-nowrap">
                            <a href="#" class="btn btn-sm btn-outline-primary" title="Lihat">
                                <i class="ri-eye-line"></i> {{-- Ganti dengan icon yang sesuai jika menggunakan library
                                yang sama (misal: Remix Icon) --}}
                            </a>
                            <a href="{{ route('productions.edit', $production->id) }}"
                                class="btn btn-sm btn-outline-secondary" title="Edit">
                                <i class="ri-pencil-line"></i>
                            </a>
                            <form action="{{ route('productions.destroy', $production->id) }}" method="POST"
                                style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Hapus produksi ini?')" title="Hapus">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada catatan produksi. <a href="{{ route('productions.create') }}">Tambahkan catatan
                                produksi baru</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- PAGINATION - Opsional, tambahkan jika $productions adalah Paginator --}}
        {{-- <div class="mt-3">
            {{ $productions->links() }}
        </div> --}}
    </div>
</div>

@endsection
