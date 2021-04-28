<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStartDateStartTimeFinishDateFinishTimeToSplsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spls', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->date('finish_date')->nullable();
            $table->time('finish_time')->nullable();
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
            $table->dropColumn('start_date');
            $table->dropColumn('start_time');
            $table->dropColumn('finish_date');
            $table->dropColumn('finish_time');
        });
    }
}
