<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllowanceIncreasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowance_increases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('year');
            $table->string('month');
            $table->unsignedBigInteger('allowance_id');
            $table->string('type_value');
            $table->string('value');
            $table->text('note')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('allowance_increases');
    }
}
