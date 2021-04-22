<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllowanceConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowance_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workgroup_id');
            $table->unsignedBigInteger('allowance_id');
            $table->string('type');
            $table->text('note')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->foreign('workgroup_id')->references('id')->on('workgroup_allowances')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('allowance_id')->references('id')->on('workgroup_allowances')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowance_configs');
    }
}
