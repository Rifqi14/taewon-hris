<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutsourcingPicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outsourcing_pics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('outsourcing_id')->nullable();
            $table->string('pic_name');
            $table->string('pic_phone');
            $table->string('pic_email');
            $table->text('pic_address');
            $table->string('pic_category');
            $table->boolean('default');
            $table->timestamps();
            $table->foreign('outsourcing_id')->references('id')->on('outsourcings')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outsourcing_pics');
    }
}
