<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllowanceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowance_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('allowance_id');
            $table->foreign('allowance_id')->references('id')->on('allowances')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('allowancedetail_id');
            $table->foreign('allowancedetail_id')->references('id')->on('allowances')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowance_details');
    }
}
