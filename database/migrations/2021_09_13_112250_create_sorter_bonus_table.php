<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSorterBonusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sorter_bonus', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('associate_id');
            $table->date('bonus_date');
            $table->unsignedInteger('ppk_shift');
            $table->unsignedInteger('bonus_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sorter_bonus');
    }
}
