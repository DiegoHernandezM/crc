<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDatesToProductivitySorterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productivity_sorter', function (Blueprint $table) {
            $table->dateTime('first_induction')->nullable();
            $table->dateTime('last_induction')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productivity_sorter', function (Blueprint $table) {
            $table->dropColumn('first_induction');
            $table->dropColumn('last_induction');
        });
    }
}
