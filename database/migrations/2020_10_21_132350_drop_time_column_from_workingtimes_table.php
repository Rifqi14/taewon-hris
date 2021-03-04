<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTimeColumnFromWorkingtimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workingtimes', function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('finish_time');
            $table->dropColumn('max_in');
            $table->dropColumn('max_out');
            $table->dropColumn('break_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workingtimes', function (Blueprint $table) {
            //
        });
    }
}