<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'production_id',
        'nama_produk',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'spesifikasi',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function production()
    {
        return $this->belongsTo(Production::class);
    }
}
