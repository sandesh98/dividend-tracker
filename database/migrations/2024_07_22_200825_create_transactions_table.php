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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->time('time')->nullable();
            $table->string('value_date')->nullable();
            $table->string('product')->nullable();
            $table->string('isin')->nullable();
            $table->string('description')->nullable();
            $table->string('fx')->nullable();
            $table->string('mutation')->nullable();
            $table->integer('mutation_value')->nullable();
            $table->string('balance')->nullable();
            $table->decimal('balance_value', 15, 2)->nullable();
            $table->string('order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
