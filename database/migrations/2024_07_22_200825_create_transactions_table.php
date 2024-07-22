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
            $table->string('date');
            $table->time('time');
            $table->string('value_date');
            $table->string('product')->nullable();
            $table->string('isin')->nullable();
            $table->string('description');
            $table->string('fx')->nullable();
            $table->string('mutation')->nullable();
            $table->decimal('mutation_value', 15, 2)->nullable();
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
