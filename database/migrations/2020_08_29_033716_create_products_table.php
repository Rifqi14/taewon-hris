<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('productcategory_id');
            $table->unsignedBigInteger('uom_id');
            $table->string('type');
            $table->string('name');
            $table->text('description');
            $table->integer('best_product');
            $table->string('merk');
            $table->integer('price');
            $table->string('image')->nullable();
            $table->integer('weight');
            $table->integer('volume_l');
            $table->integer('volume_p');
            $table->integer('volume_t');
            $table->string('condition');
            $table->string('sku');
            $table->string('barcode', 50);
            $table->integer('minimum_qty');
            $table->timestamps();

            $table->foreign('productcategory_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->foreign('uom_id')->references('id')->on('uoms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
