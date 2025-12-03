<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flower;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create sample flowers
        Flower::create([
            'name' => 'Mawar Merah',
            'kategori' => 'Mawar',
            'stock_now' => 50,
            'total_used' => 0,
            'price_per_unit' => 15000,
            'expired_at' => now()->addDays(10)
        ]);

        Flower::create([
            'name' => 'Mawar Putih',
            'kategori' => 'Mawar',
            'stock_now' => 30,
            'total_used' => 0,
            'price_per_unit' => 15000,
            'expired_at' => now()->addDays(7)
        ]);

        Flower::create([
            'name' => 'Tulip Kuning',
            'kategori' => 'Tulip',
            'stock_now' => 25,
            'total_used' => 0,
            'price_per_unit' => 12000,
            'expired_at' => now()->addDays(5)
        ]);

        Flower::create([
            'name' => 'Lily Putih',
            'kategori' => 'Lily',
            'stock_now' => 20,
            'total_used' => 0,
            'price_per_unit' => 25000,
            'expired_at' => now()->addDays(10)
        ]);

        Flower::create([
            'name' => 'Anggrek Ungu',
            'kategori' => 'Anggrek',
            'stock_now' => 15,
            'total_used' => 0,
            'price_per_unit' => 25000,
            'expired_at' => now()->addDays(12)
        ]);

        Flower::create([
            'name' => 'Krisan Merah',
            'kategori' => 'Krisan',
            'stock_now' => 60,
            'total_used' => 0,
            'price_per_unit' => 8000,
            'expired_at' => now()->addDays(4)
        ]);

        Flower::create([
            'name' => 'Gerbera Pink',
            'kategori' => 'Gerbera',
            'stock_now' => 30,
            'total_used' => 0,
            'price_per_unit' => 10000,
            'expired_at' => now()->addDays(5)
        ]);

        // Create an admin user (development convenience)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'is_admin' => true,
            ]
        );
    }
}
