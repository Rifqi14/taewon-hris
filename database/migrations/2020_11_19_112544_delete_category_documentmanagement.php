<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteCategoryDocumentmanagement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_management', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->dropColumn('code_system');
            // $table->dropForeign('site_id');
            $table->dropColumn('site_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_management', function (Blueprint $table) {
            //
        });
    }
}
