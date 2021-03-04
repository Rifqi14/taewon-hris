<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLeavetypeIdForeignLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('leave_type');
            $table->dropForeign(['leavetype_id']);
            $table->dropColumn('leavetype_id');

            $table->unsignedBigInteger('leavedetail_id')->nullable();
            $table->foreign('leavedetail_id')->references('id')->on('leave_details')->onUpdate('cascade')->onDelete('cascade');
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
            $table->unsignedBigInteger('leavetype_id');
            $table->foreign('leavetype_id')->references('id')->on('leave_balances')->onUpdate('cascade')->onDelete('cascade');

            $table->dropForeign(['leavedetail_id']);
            $table->dropColumn('leavedetail_id');
        });
    }
}