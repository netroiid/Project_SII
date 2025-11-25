<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'no_order',
        'nama_pelanggan',
        'no_telepon',
        'alamat_pengiriman',
        'tanggal_pesan',
        'tanggal_kirim',
        'total_harga',
        'metode_pembayaran',
        'status_pembayaran',
        'status_pesanan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pesan' => 'date',
        'tanggal_kirim' => 'date',
        'total_harga' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}
