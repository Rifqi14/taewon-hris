<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThrReportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thr_report_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('thr_report_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->text('description')->nullable();
            $table->float('total')->nullable();
            $table->string('is_added')->nullable();
            $table->timestamps();

            $table->foreign('thr_report_id')->references('id')->on('thr_reports')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thr_report_details');
    }
}
