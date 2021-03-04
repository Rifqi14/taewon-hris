<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakTimeLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_time_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('breaktime_id');
            $table->unsignedBigInteger('workgroup_id');
            $table->timestamps();

            $table->foreign('breaktime_id')->references('id')->on('break_times')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('workgroup_id')->references('id')->on('work_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('break_time_lines');
    }
}
