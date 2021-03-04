<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingtimeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workingtime_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workingtime_id')->nullable();
            $table->time('start')->nullable();
            $table->time('finish')->nullable();
            $table->time('min_in')->nullable();
            $table->time('max_out')->nullable();
            $table->time('break_in')->nullable();
            $table->time('break_out')->nullable();
            $table->timestamps();

            $table->foreign('workingtime_id')->references('id')->on('workingtimes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workingtime_details');
    }
}