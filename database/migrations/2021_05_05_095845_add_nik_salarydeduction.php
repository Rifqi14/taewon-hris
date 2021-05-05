<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNikSalarydeduction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_deductions', function (Blueprint $table) {
            $table->string('nik')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('workgroup_id')->nullable();
            $table->unsignedBigInteger('title_id')->nullable();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('workgroup_id')->references('id')->on('work_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('title_id')->references('id')->on('titles')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_deductions', function (Blueprint $table) {
            $table->dropColumn('nik');
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->dropForeign(['workgroup_id']);
            $table->dropColumn('workgroup_id');
        });
    }
}
