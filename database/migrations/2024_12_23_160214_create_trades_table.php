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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->time('time');
            $table->foreignId('stock_id')->constrained();
            $table->string('description');
            $table->integer('quantity');
            $table->enum('currency', ['EUR', 'USD']);
            $table->string('action')->nullable();
            $table->integer('price_per_unit');
            $table->integer('total_transaction_value');
            $table->string('fx')->nullable();
            $table->string('order_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
