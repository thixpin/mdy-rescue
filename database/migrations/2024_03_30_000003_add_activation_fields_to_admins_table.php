<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('status')->default('active');
            $table->unsignedBigInteger('activated_by')->nullable();
            $table->string('activation_status')->default('pending');
            $table->string('activation_token')->nullable();
            $table->foreign('activated_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['activated_by']);
            $table->dropColumn(['status', 'activated_by', 'activation_status', 'activation_token']);
        });
    }
};
