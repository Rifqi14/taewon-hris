<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryReportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_report_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('salary_report_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('description')->nullable();
            $table->float('total')->nullable();
            $table->integer('type')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('salary_report_id')->references('id')->on('salary_reports')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('salary_report_details');
    }
}