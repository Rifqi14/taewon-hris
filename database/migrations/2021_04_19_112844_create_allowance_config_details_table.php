<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllowanceConfigDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowance_config_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('allowance_config_id');
            $table->unsignedBigInteger('allowance_id');
            $table->timestamps();

            $table->foreign('allowance_config_id')->references('id')->on('allowance_configs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('allowance_id')->references('id')->on('allowances')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowance_config_details');
    }
}
