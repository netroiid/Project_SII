<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowersTable extends Migration
{
    public function up()
    {
        Schema::create('flowers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kategori');
            $table->integer('stock_now');
            $table->integer('total_used')->default(0);
            $table->integer('price_per_unit');
            $table->date('expired_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('flowers');
    }
}
