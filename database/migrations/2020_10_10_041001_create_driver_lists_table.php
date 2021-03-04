<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('driver_allowance_id');
            $table->string('recurrence_day')->nullable();
            $table->string('type')->nullable();
            $table->time('start')->nullable();
            $table->time('finish')->nullable();
            $table->integer('rit')->nullable();
            $table->double('value')->nullable();
            $table->timestamps();

            $table->foreign('driver_allowance_id')->references('id')->on('driver_allowances')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_lists');
    }
}