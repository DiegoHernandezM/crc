<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickingBonusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picking_bonus', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('associate_id');
            $table->date('bonus_date');
            $table->unsignedInteger('boxes_shift');
            $table->unsignedInteger('bonus_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('picking_bonus');
    }
}
