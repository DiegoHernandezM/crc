<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickingProductivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picking_productivity', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('associate_id');
            $table->unsignedInteger('wave_id');
            $table->dateTime('init_picking');
            $table->dateTime('end_picking');
            $table->decimal('minutes', 6, 2);
            $table->unsignedInteger('skus');
            $table->unsignedInteger('boxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('picking_productivity');
    }
}
