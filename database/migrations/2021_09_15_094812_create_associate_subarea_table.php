<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociateSubareaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associate_subarea', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('associate_id');
            $table->unsignedInteger('subarea_id');
            $table->dateTime('from');
            $table->dateTime('to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associate_subarea');
    }
}
