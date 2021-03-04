<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 150);
            $table->string('name', 200);
            $table->integer('order');
            $table->integer('month');
            $table->integer('bestsallary_id');
            $table->string('additional_type', 150);
            $table->string('additional_value', 100);
            $table->integer('basic_sallary');
            $table->string('increases_type', 150);
            $table->string('increases_value', 100);
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
        Schema::dropIfExists('grades');
    }
}
