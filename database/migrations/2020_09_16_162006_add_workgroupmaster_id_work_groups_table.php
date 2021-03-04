<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkgroupmasterIdWorkGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('workgroupmaster_id')->nullable()->after('id');
            $table->foreign('workgroupmaster_id')->references('id')->on('workgroup_masters')->onDelete('cascade');
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
            $table->dropForeign(['workgroup_id']);
            $table->dropColumn('workgroup_id');
            
        });
    }
}