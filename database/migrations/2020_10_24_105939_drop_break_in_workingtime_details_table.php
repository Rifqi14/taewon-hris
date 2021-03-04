<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBreakInWorkingtimeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workingtime_details', function (Blueprint $table) {
            $table->dropColumn('break_in');
            $table->dropColumn('break_out');
            $table->dropColumn('breaktime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workingtime_details', function (Blueprint $table) {
            //
        });
    }
}