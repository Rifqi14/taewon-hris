<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryIncreaseDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_increase_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('salaryincrease_id');
            $table->unsignedBigInteger('employee_id');
            $table->text('upcoming_amount');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('salaryincrease_id')->references('id')->on('salary_increases')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_increase_details');
    }
}
