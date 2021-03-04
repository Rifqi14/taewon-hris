<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_allowances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('driver')->nullable();
            $table->string('allowance')->nullable();
            $table->unsignedBigInteger('allowance_id')->nullable();
            $table->timestamps();

            $table->foreign('allowance_id')->references('id')->on('allowances')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_allowances');
    }
}