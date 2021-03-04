<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePeriodToFullDateFromToFromLeaveDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_details', function (Blueprint $table) {
            $table->date('from_balance')->nullable();
            $table->date('to_balance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_details', function (Blueprint $table) {
            $table->dropIfExists('from_balance');
            $table->dropIfExists('to_balance');
        });
    }
}