<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flower;
use App\Models\Production;
use App\Models\Order;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        $total_stok = Flower::sum('stock_now');
        $jenis_bunga = Flower::count();
        $total_produksi = Production::whereMonth('date', now()->month)->count();
        $stok_menipis = Flower::where('stock_now', '<', 10)->count();
        $kadaluarsa = Flower::whereDate('expired_at', '<=', now()->addDays(3))->count();

        // Orders summary
        $total_pesanan = Order::count();
        $pesanan_per_status = Order::selectRaw('status_pesanan, count(*) as total')
            ->groupBy('status_pesanan')->pluck('total','status_pesanan');

        // Latest orders
        $latest_orders = Order::orderBy('created_at', 'desc')->take(5)->get();

        // Top products (by ordered quantity)
        $top_products = OrderItem::selectRaw('nama_produk, sum(jumlah) as total')
            ->groupBy('nama_produk')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Stok per kategori
        $stok_per_kategori = Flower::groupBy('kategori')
            ->selectRaw('kategori, sum(stock_now) as total')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->kategori => $item->total];
            });

        // Low stock and expiring
        $low_stock_flowers = Flower::where('stock_now', '<', 10)->get();
        $expiring_flowers = Flower::whereDate('expired_at', '<=', now()->addDays(3))->get();

        $produksi_harian = Production::selectRaw('date, count(*) as total')
            ->where('date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get();

        return view('dashboard', compact(
            'total_stok', 'jenis_bunga', 'total_produksi',
            'stok_menipis', 'kadaluarsa', 'stok_per_kategori', 'produksi_harian',
            'total_pesanan', 'pesanan_per_status', 'latest_orders', 'top_products',
            'low_stock_flowers', 'expiring_flowers'
        ));
    }
}
