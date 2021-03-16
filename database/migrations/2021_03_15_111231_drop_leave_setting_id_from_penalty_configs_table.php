<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLeaveSettingIdFromPenaltyConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penalty_configs', function (Blueprint $table) {
            $table->dropForeign(['leave_setting_id']);
            $table->dropColumn('leave_setting_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penalty_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('leave_setting_id')->nullable();
            $table->foreign('leave_setting_id')->references('id')->on('leave_settings')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}