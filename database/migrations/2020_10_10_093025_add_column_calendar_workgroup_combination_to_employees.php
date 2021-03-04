<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCalendarWorkgroupCombinationToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('calendar_id')->nullable();
            $table->unsignedBigInteger('workgroup_id')->nullable();
            $table->foreign('calendar_id')->references('id')->on('calendars')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('workgroup_id')->references('id')->on('work_groups')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('calendar_id');
            $table->dropColumn('workgroup_id');
        });
    }
}
