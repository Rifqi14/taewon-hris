<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nid')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('type');
            $table->string('gender');
            $table->unsignedBigInteger('place_of_birth');
            $table->date('birth_date');
            $table->foreign('place_of_birth')->references('id')->on('regions')->onDelete('restrict');
            $table->string('address');
            $table->double('latitude',15,8)->nullable();
            $table->double('longitude',15,8)->nullable();
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
        Schema::dropIfExists('employees');
    }
}
