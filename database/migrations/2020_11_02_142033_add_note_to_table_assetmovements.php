<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoteToTableAssetmovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_movements', function (Blueprint $table) {
            $table->text('note')->nullable();
            $table->dropColumn('production_no');
            $table->dropColumn('ref_no');
            $table->dropColumn('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_movements', function (Blueprint $table) {
            //
        });
    }
}
