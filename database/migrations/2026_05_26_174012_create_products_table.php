<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('link')->nullable();
            $table->text('image_url')->nullable();
            $table->string('brand')->nullable();
            $table->string('condition')->nullable();
            $table->string('availability')->nullable();
            $table->decimal('quantity', 14, 4)->nullable();
            $table->decimal('price', 14, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
