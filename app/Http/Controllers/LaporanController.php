<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flower;
use App\Models\Production;
use App\Models\OrderItem;

class LaporanController extends Controller
{
    public function index()
    {
        // Total nilai stok (sum stock_now * price_per_unit)
        $flowers = Flower::all();
        $nilai_total_stok = $flowers->reduce(function ($carry, $item) {
            return $carry + ($item->stock_now * $item->price_per_unit);
        }, 0);

        $produksi_bulan_ini = Production::whereMonth('date', now()->month)->count();

        $alert_stok = Flower::where('stock_now', '<', 10)->count();

        // Laporan stok detail
        $laporan_stok = $flowers->map(function ($f) {
            return (object) [
                'name' => $f->name,
                'kategori' => $f->kategori,
                'stock_now' => $f->stock_now,
                'nilai_stok' => $f->stock_now * $f->price_per_unit,
                'status' => $f->stock_now < 10 ? 'Stok menipis' : null,
            ];
        });

        // Penggunaan bahan bulan ini (from pivot flower_production)
        $from = now()->startOfMonth();
        $to = now()->endOfMonth();

        $penggunaan = \DB::table('flower_production')
            ->join('productions', 'flower_production.production_id', '=', 'productions.id')
            ->join('flowers', 'flower_production.flower_id', '=', 'flowers.id')
            ->whereBetween('productions.date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('flowers.name as flower_name, sum(flower_production.quantity_used) as total_used')
            ->groupBy('flowers.name')
            ->get();

        return view('laporan.index', compact('nilai_total_stok', 'produksi_bulan_ini', 'alert_stok', 'laporan_stok', 'penggunaan'));
    }
}
