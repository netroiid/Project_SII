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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Foreign key to orders
            $table->foreignId('production_id')->nullable()->constrained('productions')->onDelete('set null'); // Foreign key to productions
            $table->string('nama_produk'); // Product/flower name
            $table->integer('jumlah')->default(1); // Quantity
            $table->decimal('harga_satuan', 12, 2); // Unit price
            $table->decimal('subtotal', 12, 2); // Subtotal (jumlah * harga_satuan)
            $table->text('spesifikasi')->nullable(); // Special notes/specifications
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
