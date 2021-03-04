<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table)
        {
            $table->integer('position_id');
            $table->integer('department_id');
            $table->string('workgroup_combination');
            $table->integer('grade_id');
            $table->string('nik');
            $table->string('npwp');
            $table->integer('province_id');
            $table->string('account_bank');
            $table->string('account_no');
            $table->string('account_name');
            $table->string('emergency_contact_no');
            $table->string('emergency_contact_name');
            $table->string('working_time_type');
            $table->string('working_time');
            $table->integer('status');
            $table->text('notes')->nullable();
            $table->string('join');
            $table->string('photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
