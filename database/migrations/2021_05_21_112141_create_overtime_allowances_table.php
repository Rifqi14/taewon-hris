<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOvertimeAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overtime_allowances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('overtime_scheme_id')->nullable();
            $table->unsignedBigInteger('allowance_id')->nullable();
            $table->timestamps();

            $table->foreign('overtime_scheme_id')->references('id')->on('overtime_schemes')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('overtime_allowances');
    }
}
