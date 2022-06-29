<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductivitySorterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productivity_sorter', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('associate_id')->index();
            $table->integer('wave')->index();
            $table->date('date');
            $table->bigInteger('inductions');
            $table->float('active_time')->nullable();
            $table->float('total_time')->nullable();
            $table->string('sorter');
            $table->bigInteger('pieces');
            $table->bigInteger('ppk');
            $table->bigInteger('bono')->nullable();
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
        Schema::dropIfExists('productivity_sorter');
    }
}
