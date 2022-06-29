<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusStaffManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_staff_managers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('associate_id')->index();
            $table->unsignedInteger('area_id')->index();
            $table->unsignedInteger('subarea_id')->index();
            $table->bigInteger('year_week')->nullable();
            $table->unsignedInteger('bonus_amount')->nullable();
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
        Schema::dropIfExists('bonus_staff_managers');
    }
}
