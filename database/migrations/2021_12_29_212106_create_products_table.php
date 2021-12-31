<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('name')->unique();
            $table->string('sku')->unique();
            $table->integer('price')->default(0);
            $table->integer('qty')->default(0);
            $table->text('image')->nullable();
            $table->longText('description')->nullable();
            $table->foreignIdFor(\App\Models\Brand::class)->OnDelete('cascade');
            $table->foreignIdFor(\App\Models\Category::class)->OnDelete('cascade');
            $table->foreignIdFor(\App\Models\User::class)->OnDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
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
