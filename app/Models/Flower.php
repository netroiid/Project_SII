<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flower extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'kategori',
        'stock_now',
        'total_used',
        'price_per_unit',
        'expired_at'
    ];

    public function productions()
    {
        return $this->belongsToMany(Production::class, 'flower_production')
            ->withPivot('quantity_used')
            ->withTimestamps();
    }
}

