<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPeriodIdToEmployeeAllowances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_allowances', function (Blueprint $table) {
            $table->dropColumn('factor');
            $table->dropColumn('year');
            $table->dropColumn('month');
        });

        Schema::table('employee_allowances', function (Blueprint $table) {
            $table->decimal('factor', 8,3)->nullable();
            $table->year('year')->nullable();
            $table->string('month',2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_allowances', function (Blueprint $table) {
            $table->decimal('factor', 8,3)->nullable();
            $table->year('year')->nullable();
            $table->string('month',2)->nullable();
        });
        
        Schema::table('employee_allowances', function (Blueprint $table) {
            $table->dropColumn('factor');
            $table->dropColumn('year');
            $table->dropColumn('month');
        });
    }
}