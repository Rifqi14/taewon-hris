<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOvertimeSchemeListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overtime_scheme_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('overtime_scheme_id')->nullable();
            $table->string('recurrence_day')->nullable();
            $table->integer('hour')->nullable();
            $table->double('amount')->nullable();
            $table->timestamps();

            $table->foreign('overtime_scheme_id')->references('id')->on('overtime_schemes')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('overtime_scheme_lists');
    }
}