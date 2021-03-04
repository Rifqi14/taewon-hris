<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyReportDriverDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_report_driver_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('daily_report_driver_id');
            $table->string('destination')->nullable();
            $table->time('departure')->nullable();
            $table->double('departure_km')->nullable();
            $table->time('arrival')->nullable();
            $table->double('arrival_km')->nullable();
            $table->double('parking')->nullable();
            $table->double('toll_money')->nullable();
            $table->double('police')->nullable();
            $table->double('total')->nullable();
            $table->timestamps();

            $table->foreign('daily_report_driver_id')->references('id')->on('daily_report_drivers')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_report_driver_details');
    }
}
