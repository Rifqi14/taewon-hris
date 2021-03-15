<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenaltyConfigDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penalty_config_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('penalty_config_id')->nullable();
            $table->unsignedBigInteger('allowance_id')->nullable();
            $table->timestamps();

            $table->foreign('penalty_config_id')->references('id')->on('penalty_configs')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('penalty_config_details');
    }
}