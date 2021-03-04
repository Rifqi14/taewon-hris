<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeWorkgroupWorkGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_groups', function (Blueprint $table) {
            $table->renameColumn('workgroup', 'workgroup_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_groups', function (Blueprint $table) {
            $table->renameColumn('workgroup_id', 'workgroup');
        });
    }
}
