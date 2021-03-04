<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePphReportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pph_report_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pph_report_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('description')->nullable();
            $table->float('total')->nullable();
            $table->integer('type')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('pph_report_id')->references('id')->on('pph_reports')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('pph_report_details');
    }
}
