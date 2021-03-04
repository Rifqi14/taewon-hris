<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxInAndMaxOutToWorkingtimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workingtimes', function (Blueprint $table) {
            $table->time('max_in')->nullable();
            $table->time('max_out')->nullable();
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
            $table->dropColumn('max_in');
            $table->dropColumn('max_out');
        });
    }
}