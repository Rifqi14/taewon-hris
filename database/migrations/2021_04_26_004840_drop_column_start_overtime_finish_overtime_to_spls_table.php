<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnStartOvertimeFinishOvertimeToSplsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spls', function (Blueprint $table) {
            $table->dropColumn('start_overtime');
            $table->dropColumn('finish_overtime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spls', function (Blueprint $table) {
            //
        });
    }
}
