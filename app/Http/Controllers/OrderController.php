<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Production;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('items')->orderBy('tanggal_pesan', 'desc')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productions = Production::all();
        $productTemplates = config('products');
        $latestOrder = Order::latest('id')->first();
        $nextNo = $latestOrder ? 'ORD-' . str_pad((intval(substr($latestOrder->no_order, 4)) + 1), 3, '0', STR_PAD_LEFT) : 'ORD-001';

        return view('orders.create', compact('productions', 'nextNo', 'productTemplates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_order' => 'required|unique:orders',
            'nama_pelanggan' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat_pengiriman' => 'required|string',
            'tanggal_pesan' => 'required|date',
            'tanggal_kirim' => 'required|date|after_or_equal:tanggal_pesan',
            'metode_pembayaran' => 'required|in:transfer,tunai',
            'status_pembayaran' => 'required|in:belum_bayar,sudah_bayar',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.production_id' => 'nullable|exists:productions,id',
            'items.*.nama_produk' => 'required|string|max:255',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.spesifikasi' => 'nullable|string',
        ]);

        $totalHarga = 0;
        foreach ($validated['items'] as $item) {
            $totalHarga += $item['jumlah'] * $item['harga_satuan'];
        }

        $order = Order::create([
            'no_order' => $validated['no_order'],
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'no_telepon' => $validated['no_telepon'],
            'alamat_pengiriman' => $validated['alamat_pengiriman'],
            'tanggal_pesan' => $validated['tanggal_pesan'],
            'tanggal_kirim' => $validated['tanggal_kirim'],
            'total_harga' => $totalHarga,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'status_pembayaran' => $validated['status_pembayaran'],
            'status_pesanan' => 'pending',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $subtotal = $item['jumlah'] * $item['harga_satuan'];
            OrderItem::create([
                'order_id' => $order->id,
                'production_id' => $item['production_id'] ?? null,
                'nama_produk' => $item['nama_produk'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'subtotal' => $subtotal,
                'spesifikasi' => $item['spesifikasi'] ?? null,
            ]);
        }

        return redirect()->route('pesanan.show', $order->id)->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $pesanan)
    {
        $pesanan->load('items');
        return view('orders.show', compact('pesanan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $pesanan)
    {
        $productions = Production::all();
        $pesanan->load('items');
        return view('orders.edit', compact('pesanan', 'productions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $pesanan)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat_pengiriman' => 'required|string',
            'tanggal_pesan' => 'required|date',
            'tanggal_kirim' => 'required|date|after_or_equal:tanggal_pesan',
            'metode_pembayaran' => 'required|in:transfer,tunai',
            'status_pembayaran' => 'required|in:belum_bayar,sudah_bayar',
            'status_pesanan' => 'required|in:pending,proses,dikirim,selesai',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.production_id' => 'nullable|exists:productions,id',
            'items.*.nama_produk' => 'required|string|max:255',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.spesifikasi' => 'nullable|string',
        ]);

        $totalHarga = 0;
        foreach ($validated['items'] as $item) {
            $totalHarga += $item['jumlah'] * $item['harga_satuan'];
        }

        $pesanan->update([
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'no_telepon' => $validated['no_telepon'],
            'alamat_pengiriman' => $validated['alamat_pengiriman'],
            'tanggal_pesan' => $validated['tanggal_pesan'],
            'tanggal_kirim' => $validated['tanggal_kirim'],
            'total_harga' => $totalHarga,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'status_pembayaran' => $validated['status_pembayaran'],
            'status_pesanan' => $validated['status_pesanan'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Delete existing items and create new ones
        $pesanan->items()->delete();
        foreach ($validated['items'] as $item) {
            $subtotal = $item['jumlah'] * $item['harga_satuan'];
            OrderItem::create([
                'order_id' => $pesanan->id,
                'production_id' => $item['production_id'] ?? null,
                'nama_produk' => $item['nama_produk'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'subtotal' => $subtotal,
                'spesifikasi' => $item['spesifikasi'] ?? null,
            ]);
        }

        return redirect()->route('pesanan.show', $pesanan->id)->with('success', 'Pesanan berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $pesanan)
    {
        $pesanan->items()->delete();
        $pesanan->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus!');
    }
}

