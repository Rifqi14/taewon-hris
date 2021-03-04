<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsumeOilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consume_oils', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('oil_id');
            $table->string('engine_oil');
            $table->double('km');
            $table->string('driver');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('assets')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('oil_id')->references('id')->on('assets')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consume_oil');
    }
}
