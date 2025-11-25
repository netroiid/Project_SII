<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'date', 'product_name', 'type', 'quantity', 'customer'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function flowers()
    {
        return $this->belongsToMany(Flower::class, 'flower_production')
            ->withPivot('quantity_used')
            ->withTimestamps();
    }
}
