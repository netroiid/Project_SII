<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_order')->unique(); // ORD-001, ORD-002
            $table->string('nama_pelanggan'); // Customer name
            $table->string('no_telepon'); // Phone number
            $table->text('alamat_pengiriman'); // Delivery address
            $table->date('tanggal_pesan'); // Order date
            $table->date('tanggal_kirim'); // Delivery date
            $table->decimal('total_harga', 12, 2)->default(0); // Total price
            $table->enum('metode_pembayaran', ['transfer', 'tunai'])->default('tunai'); // Payment method
            $table->enum('status_pembayaran', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar'); // Payment status
            $table->enum('status_pesanan', ['pending', 'proses', 'dikirim', 'selesai'])->default('pending'); // Order status
            $table->text('catatan')->nullable(); // Notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
