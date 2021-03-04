<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkgroupWorkGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_groups', function (Blueprint $table) {
            // $table->integer('workgroup_id')->nullable()->after('id');
            // $table->foreign('workgroup_id')->references('id')->on('workgroup_masters')->onDelete('cascade');
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
            //
        });
    }
}