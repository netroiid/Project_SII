<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flower;
use App\Models\Production;
use App\Models\OrderItem;
// PhpSpreadsheet (optional). If not installed, export will fall back to CSV.
// PDF generation only (Dompdf / barryvdh wrapper expected)

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

        // Laporan stok detail - pisahkan status menjadi kesegaran (kadaluarsa) dan stok
        $laporan_stok = $flowers->map(function ($f) {
            // status kesegaran/kadaluarsa
            $status_kesegaran = 'Segar';
            if ($f->expired_at) {
                $expiredDate = \Carbon\Carbon::parse($f->expired_at)->startOfDay();
                $today = \Carbon\Carbon::today();
                $daysRemaining = $today->diffInDays($expiredDate, false);
                if ($daysRemaining < 0) {
                    $status_kesegaran = 'Kadaluarsa';
                } elseif ($daysRemaining <= 3) {
                    $status_kesegaran = 'Segera Habis';
                }
            }

            // status stok
            if ($f->stock_now <= 0) {
                $status_stok = 'Habis';
            } elseif ($f->stock_now < 10) {
                $status_stok = 'Menipis';
            } else {
                $status_stok = 'Aman';
            }

            return (object) [
                'name' => $f->name,
                'kategori' => $f->kategori,
                'stock_now' => $f->stock_now,
                'nilai_stok' => $f->stock_now * $f->price_per_unit,
                'status_kesegaran' => $status_kesegaran,
                'status_stok' => $status_stok,
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

    /**
     * Export laporan menjadi file Excel (.xlsx).
     * Jika PhpSpreadsheet tidak tersedia, akan fallback ke CSV.
     */
    public function export(Request $request)
    {
        $flowers = Flower::all();

        $nilai_total_stok = $flowers->reduce(function ($carry, $item) {
            return $carry + ($item->stock_now * $item->price_per_unit);
        }, 0);

        $produksi_bulan_ini = Production::whereMonth('date', now()->month)->count();
        $alert_stok = Flower::where('stock_now', '<', 10)->count();

        $laporan_stok = $flowers->map(function ($f) {
            // status kesegaran/kadaluarsa
            $status_kesegaran = 'Segar';
            if ($f->expired_at) {
                $expiredDate = \Carbon\Carbon::parse($f->expired_at)->startOfDay();
                $today = \Carbon\Carbon::today();
                $daysRemaining = $today->diffInDays($expiredDate, false);
                if ($daysRemaining < 0) {
                    $status_kesegaran = 'Kadaluarsa';
                } elseif ($daysRemaining <= 3) {
                    $status_kesegaran = 'Segera Habis';
                }
            }

            // status stok
            if ($f->stock_now <= 0) {
                $status_stok = 'Habis';
            } elseif ($f->stock_now < 10) {
                $status_stok = 'Menipis';
            } else {
                $status_stok = 'Aman';
            }

            return (object) [
                'name' => $f->name,
                'kategori' => $f->kategori,
                'stock_now' => $f->stock_now,
                'nilai_stok' => $f->stock_now * $f->price_per_unit,
                'status_kesegaran' => $status_kesegaran,
                'status_stok' => $status_stok,
            ];
        });

        // Penggunaan bahan bulan ini
        $from = now()->startOfMonth();
        $to = now()->endOfMonth();

        $penggunaan = \DB::table('flower_production')
            ->join('productions', 'flower_production.production_id', '=', 'productions.id')
            ->join('flowers', 'flower_production.flower_id', '=', 'flowers.id')
            ->whereBetween('productions.date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('flowers.id as flower_id, flowers.name as flower_name, sum(flower_production.quantity_used) as total_used')
            ->groupBy('flowers.id','flowers.name')
            ->get();

        // Tambahkan tampilan persentase untuk PDF
        $penggunaan = $penggunaan->map(function ($p) {
            $flowerModel = Flower::find($p->flower_id);
            $stockAwal = $flowerModel ? ($flowerModel->stock_now + $p->total_used) : null;
            $percent = $stockAwal ? round(($p->total_used / $stockAwal) * 100, 2) : null;
            $p->percent_display = $percent !== null ? $percent . '%' : '-';
            return $p;
        });

        // Peringatan: stok rendah / kadaluarsa
        $peringatan = collect();
        foreach ($flowers as $f) {
            $expired = null;
            if ($f->expired_at) {
                $expiredDate = \Carbon\Carbon::parse($f->expired_at)->startOfDay();
                $today = \Carbon\Carbon::today();
                $daysRemaining = $today->diffInDays($expiredDate, false);
                if ($daysRemaining < 0) {
                    $expired = 'Kadaluarsa';
                } elseif ($daysRemaining <= 3) {
                    $expired = 'Segera Habis (' . $daysRemaining . ' hari)';
                }
            }

            if ($f->stock_now < 10 || $expired) {
                $peringatan->push((object) [
                    'name' => $f->name,
                    'stock_now' => $f->stock_now,
                    'expired_note' => $expired,
                ]);
            }
        }

            // Coba buat PDF terlebih dahulu (jika Dompdf / barryvdh tersedia)
            $pdfName = 'laporan_' . now()->format('Ymd_His') . '.pdf';
            $viewData = compact('nilai_total_stok', 'produksi_bulan_ini', 'alert_stok', 'laporan_stok', 'penggunaan', 'peringatan');

            if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf') ) {
                // Barryvdh wrapper tersedia
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf', $viewData);
                return $pdf->setPaper('a4', 'portrait')->download($pdfName);
            }

            if (class_exists('\\Dompdf\\Dompdf')) {
                $html = view('laporan.pdf', $viewData)->render();
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $dompdf->stream($pdfName, ['Attachment' => true]);
                exit;
            }

            // Jika tidak ada library PDF, tampilkan instruksi instalasi
            return response()->json([
                'error' => 'PDF library not installed. Install barryvdh/laravel-dompdf or dompdf/dompdf via composer to enable PDF export.'
            ], 500);
    }
}
