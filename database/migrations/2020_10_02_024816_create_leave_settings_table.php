<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('leave_name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->double('balance')->nullable();
            $table->string('reset_time')->nullable();
            $table->date('specific_date')->nullable();
            $table->string('use_time')->nullable();
            $table->string('label_color')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->nullable();
            $table->integer('description')->nullable();
            $table->integer('level')->nullable();
            $table->text('path')->nullable();
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
        Schema::dropIfExists('leave_settings');
    }
}