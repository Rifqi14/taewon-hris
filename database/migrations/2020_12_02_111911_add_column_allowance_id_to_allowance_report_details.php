<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAllowanceIdToAllowanceReportDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowance_report_details', function (Blueprint $table) {
            $table->unsignedBigInteger('allowance_id')->nullable();
            $table->foreign('allowance_id')->references('id')->on('allowances')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allowance_report_details', function (Blueprint $table) {
            //
        });
    }
}
