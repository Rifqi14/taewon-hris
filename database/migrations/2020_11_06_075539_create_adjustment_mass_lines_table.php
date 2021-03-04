<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdjustmentMassLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustment_mass_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('adjustmentmass_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamps();

            $table->foreign('adjustmentmass_id')->references('id')->on('adjustment_masses')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('adjustment_mass_lines');
    }
}
