<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); 
            $table->enum('type', ['entry', 'exit']); 
            $table->integer('quantity'); 
            $table->string('reason'); 
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('movements');
    }
}