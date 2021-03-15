<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenaltyConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penalty_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workgroup_id')->nullable();
            $table->unsignedBigInteger('leave_setting_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('is_basic_salary')->default('NO');
            $table->string('status', 20)->nullable();
            $table->timestamps();

            $table->foreign('workgroup_id')->references('id')->on('work_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('leave_setting_id')->references('id')->on('leave_settings')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penalty_configs');
    }
}