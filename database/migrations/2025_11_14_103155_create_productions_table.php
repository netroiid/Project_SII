<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration
{
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('product_name');
            $table->enum('type', ['Buket', 'Rangkaian Meja', 'Dekorasi']);
            $table->integer('quantity');
            $table->string('customer');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productions');
    }
}
