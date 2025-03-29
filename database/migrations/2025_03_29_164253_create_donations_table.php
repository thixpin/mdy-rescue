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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('short_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('donation_amount', 10, 2);
            $table->string('amount_in_text');
            $table->dateTime('donate_date');
            $table->boolean('verified')->default(false);
            $table->string('certificate_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
