<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnToEmployeeAllowances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_allowances', function (Blueprint $table) {
            $table->renameColumn('workgroupallowance_id', 'allowance_id');
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
            $table->renameColumn('allowance_id', 'workgroupallowance_id');
        });
    }
}
