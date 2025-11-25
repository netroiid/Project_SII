<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\Flower;
use Illuminate\Support\Facades\DB;
use App\Models\Order; // Diperlukan jika Order digunakan di create/update

class ProductionController extends Controller
{
    public function index()
    {
        $productions = Production::with('flowers')->get();
        return view('productions.index', compact('productions'));
    }

    public function create()
    {
        $flowers = Flower::all();
        $productTemplates = config('products');
        $order = null;
        $order_id = request()->query('order_id');

        if ($order_id) {
            $order = Order::with('items')->findOrFail($order_id);
        }

        return view('productions.create', compact('flowers', 'order', 'productTemplates'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'date' => 'required|date',
            'product_name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'customer' => 'nullable|string|max:255',
            'flowers.*.id' => 'required|exists:flowers,id',
            'flowers.*.quantity' => 'nullable|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $production = Production::create($request->only(['order_id', 'date','product_name','type','quantity','customer']));
                $flowersData = $request->input('flowers', []);
                $attachments = [];
                $hasMaterial = false;

                foreach ($flowersData as $flower_data) {
                    $flowerId = $flower_data['id'];
                    $quantity_used = $flower_data['quantity'] ?? 0;

                    if ($quantity_used > 0) {
                        $flower = Flower::findOrFail($flowerId);
                        // Cek stok sebelum membuat produksi baru
                        if ($flower->stock_now < $quantity_used) {
                            throw new \Exception('Stok bunga ' . $flower->name . ' tidak cukup! Stok tersedia: ' . $flower->stock_now);
                        }
                        // Kurangi stok dan tambah total_used
                        $flower->stock_now -= $quantity_used;
                        $flower->total_used += $quantity_used;
                        $flower->save();

                        $attachments[$flowerId] = ['quantity_used' => $quantity_used];
                        $hasMaterial = true;
                    }
                }

                if (!$hasMaterial) {
                    throw new \Exception('Minimal harus ada 1 bahan untuk produksi!');
                }

                // Attach ke production
                $production->flowers()->attach($attachments);
            });

            return redirect()->route('productions.index')->with('success', 'Produksi berhasil dibuat dan stok berkurang!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan formulir untuk mengedit sumber daya yang ditentukan.
     * DITAMBAHKAN
     *
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function edit(Production $production)
    {
        $allFlowers = Flower::all();
        // Mengirim production yang di-load dengan relasi flowers-nya sudah otomatis oleh Route Model Binding

        return view('productions.edit', compact('production', 'allFlowers'));
    }

    /**
     * Perbarui sumber daya yang ditentukan di penyimpanan.
     * DITAMBAHKAN DAN MENGANDUNG LOGIKA PENGEMBALIAN STOK LAMA
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Production $production)
    {
        // 1. Validasi Input
        $request->validate([
            'date' => 'required|date',
            'product_name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'customer' => 'nullable|string|max:255',
            'materials' => 'array',
            'materials.*' => 'nullable|integer|min:0', // Kuantitas bahan baku
        ]);

        try {
            DB::transaction(function () use ($request, $production) {

                // 2. KEMBALIKAN STOK LAMA (Revert Stock)
                // Iterasi melalui bahan yang sebelumnya terpakai pada produksi ini
                foreach ($production->flowers as $oldFlower) {
                    $quantity_used_old = $oldFlower->pivot->quantity_used;

                    // Kembalikan stok yang sebelumnya terpakai
                    $oldFlower->stock_now += $quantity_used_old;
                    $oldFlower->total_used -= $quantity_used_old;
                    $oldFlower->save();
                }

                // Detach semua relasi lama sebelum attach yang baru
                $production->flowers()->detach();

                // 3. Update Data Produksi Utama
                $production->update($request->only(['date','product_name','type','quantity','customer']));

                // 4. Proses Bahan Baku Baru dan Kurangi Stok (New Stock Deduction)
                $materialsData = $request->input('materials', []);
                $attachments = [];
                $hasMaterial = false;

                foreach ($materialsData as $flowerId => $quantity_used_new) {
                    $quantity_used_new = (int) $quantity_used_new;

                    if ($quantity_used_new > 0) {
                        $flower = Flower::findOrFail($flowerId);

                        // Cek stok: Stok saat ini sudah di-revert pada langkah 2.
                        if ($flower->stock_now < $quantity_used_new) {
                            // Jika stok tidak cukup, batalkan transaksi dan lemparkan error
                            throw new \Exception('Stok bunga ' . $flower->name . ' tidak cukup untuk perubahan ini! Stok tersedia: ' . $flower->stock_now);
                        }

                        // Kurangi stok dan tambah total_used dengan kuantitas BARU
                        $flower->stock_now -= $quantity_used_new;
                        $flower->total_used += $quantity_used_new;
                        $flower->save();

                        $attachments[$flowerId] = ['quantity_used' => $quantity_used_new];
                        $hasMaterial = true;
                    }
                }

                if (!$hasMaterial) {
                    // Jika user menghapus semua bahan baku
                    throw new \Exception('Minimal harus ada 1 bahan untuk produksi!');
                }

                // Attach relasi baru
                $production->flowers()->attach($attachments);
            });

            return redirect()->route('productions.index')->with('success', 'Produksi berhasil diperbarui!');
        } catch (\Exception $e) {
            // Jika terjadi error (misalnya stok tidak cukup), transaksi DIBATALKAN.
            // Namun, karena stok lama sudah di-revert di awal transaksi, kita perlu memastikan
            // state database konsisten. Penggunaan DB::transaction akan mengurus ini (rollback).
            return back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function destroy(Production $production)
    {
        // Lakukan pengembalian stok dalam transaksi untuk keamanan data
        try {
            DB::transaction(function () use ($production) {
                // Kembalikan stok jika produksi dihapus
                foreach ($production->flowers as $flower) {
                    $flower->stock_now += $flower->pivot->quantity_used;
                    $flower->total_used -= $flower->pivot->quantity_used;
                    $flower->save();
                }
                $production->flowers()->detach();
                $production->delete();
            });
            return redirect()->route('productions.index')->with('success', 'Produksi berhasil dihapus dan stok dikembalikan!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produksi. ' . $e->getMessage());
        }
    }
}
