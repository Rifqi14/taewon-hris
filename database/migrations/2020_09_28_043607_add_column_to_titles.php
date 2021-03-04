<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTitles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->string('code', 150);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('max_person');
            $table->text('notes')->nullable();
            $table->integer('level')->nullable();
            $table->text('path')->nullable();
            $table->string('code_system')->nullable();
            $table->string('status', 200);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('parent_id');
            $table->dropColumn('max_person');
            $table->dropColumn('notes');
            $table->dropColumn('level');
            $table->dropColumn('path');
            $table->dropColumn('code_system');
            $table->dropColumn('status');
        });
    }
}