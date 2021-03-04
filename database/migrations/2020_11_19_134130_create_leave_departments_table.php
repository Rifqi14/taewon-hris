<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leave_setting_id')->nullable()->references('id')->on('leave_settings')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('department_id')->nullable()->references('id')->on('departments')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('leave_departments');
    }
}