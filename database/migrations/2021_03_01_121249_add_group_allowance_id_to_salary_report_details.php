<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupAllowanceIdToSalaryReportDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_report_details', function (Blueprint $table) {
            $table->unsignedBigInteger('group_allowance_id')->nullable();
            $table->foreign('group_allowance_id')->references('id')->on('group_allowances')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_report_details', function (Blueprint $table) {
            $table->dropForeign(['group_allowance_id']);
            $table->dropColumn('group_allowance_id');
        });
    }
}