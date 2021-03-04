<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutsourcingDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outsourcing_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('outsourcing_id')->nullable();
            $table->string('category');
            $table->string('phone');
            $table->string('name');
            $table->string('file');
            $table->string('description');
            $table->foreign('outsourcing_id')->references('id')->on('outsourcings')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outsourcing_documents');
    }
}
