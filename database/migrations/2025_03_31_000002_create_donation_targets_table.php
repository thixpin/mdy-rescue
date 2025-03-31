<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First create the donation_targets table
        if (!Schema::hasTable('donation_targets')) {
            Schema::create('donation_targets', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->decimal('target_amount', 15, 2);
                $table->decimal('current_amount', 15, 2)->default(0);
                $table->date('start_date');
                $table->date('close_date');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Now modify the donations table
        if (Schema::hasTable('donations')) {
            // Check if the column exists before adding it
            if (!Schema::hasColumn('donations', 'donation_target_id')) {
                Schema::table('donations', function (Blueprint $table) {
                    $table->unsignedBigInteger('donation_target_id')->nullable()->after('id');
                });
            }

            // Check if general purpose target exists
            $generalPurposeExists = DB::table('donation_targets')
                ->where('name', 'General Purpose')
                ->exists();

            if (!$generalPurposeExists) {
                // Create the general purpose target
                $generalPurposeId = DB::table('donation_targets')->insertGetId([
                    'name' => 'General Purpose',
                    'description' => 'General purpose donations for various needs',
                    'target_amount' => 1000000,
                    'current_amount' => 0,
                    'start_date' => now(),
                    'close_date' => now()->addYear(),
                    'is_active' => true,
                    'image_url' => 'https://via.placeholder.com/150',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update existing donations to use the general purpose target
                DB::table('donations')->update(['donation_target_id' => $generalPurposeId]);
            }

            // Add foreign key constraint
            Schema::table('donations', function (Blueprint $table) {
                if (!Schema::hasColumn('donations', 'donation_target_id')) {
                    $table->unsignedBigInteger('donation_target_id')->after('id');
                }
                $table->foreign('donation_target_id')->references('id')->on('donation_targets');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('donations')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropForeign(['donation_target_id']);
                $table->dropColumn('donation_target_id');
            });
        }
        
        Schema::dropIfExists('donation_targets');
    }
}; 