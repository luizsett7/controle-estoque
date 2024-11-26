<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->string('code')->unique(); 
            $table->text('description')->nullable(); 
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade'); 
            $table->decimal('cost_price', 10, 2); 
            $table->decimal('sale_price', 10, 2); 
            $table->integer('min_stock'); 
            $table->integer('stock');
            $table->date('expiration_date')->nullable(); 
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}