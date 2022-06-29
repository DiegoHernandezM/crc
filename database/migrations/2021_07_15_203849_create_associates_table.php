<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->index();
            $table->unsignedInteger('area_id')->index();
            $table->unsignedInteger('subarea_id')->index();
            $table->unsignedInteger('shift_id')->index();
            $table->unsignedInteger('associate_type_id')->nullable()->index();
            $table->unsignedInteger('employee_number')->index();
            $table->dateTime('entry_date')->index();
            $table->unsignedInteger('status_id')->index();
            $table->boolean('elegible')->index();
            $table->string('picture')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associates');
    }
}
