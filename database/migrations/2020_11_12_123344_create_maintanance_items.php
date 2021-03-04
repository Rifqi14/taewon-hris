<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintananceItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintanance_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('maintanance_id');
            $table->string('item');
            $table->double('cost');
            $table->timestamps();

            $table->foreign('maintanance_id')->references('id')->on('maintanances')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintanance_items');
    }
}
