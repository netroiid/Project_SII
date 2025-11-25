<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowerProductionTable extends Migration
{
    public function up()
    {
        Schema::create('flower_production', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flower_id')->constrained('flowers')->onDelete('cascade');
            $table->foreignId('production_id')->constrained('productions')->onDelete('cascade');
            $table->integer('quantity_used');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('flower_production');
    }
}
