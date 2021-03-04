<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForeignLeavedetailIdFromLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['leavedetail_id']);
            $table->dropColumn('leavedetail_id');
            $table->unsignedBigInteger('leave_setting_id')->nullable()->references('id')->on('leave_settings')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['leave_setting_id']);
            $table->dropColumn('leave_setting_id');
            $table->unsignedInteger('leavedetail_id')->nullable()->references('id')->on('leave_details')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}