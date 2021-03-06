<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarShiftSwitchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_shift_switches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('calendar_exceptions_id')->nullable();
            $table->unsignedBigInteger('workingtime_id')->nullable();
            $table->time('start')->nullable();
            $table->time('finish')->nullable();
            $table->time('min_in')->nullable();
            $table->time('max_out')->nullable();
            $table->integer('workhour')->nullable();
            $table->string('day')->nullable();
            $table->integer('min_workhour')->nullable();
            $table->timestamps();

            $table->foreign('calendar_exceptions_id')->references('id')->on('calendar_exceptions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('workingtime_id')->references('id')->on('workingtimes')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_shift_switches');
    }
}