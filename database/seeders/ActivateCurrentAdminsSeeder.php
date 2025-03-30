<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class ActivateCurrentAdminsSeeder extends Seeder
{
    public function run(): void
    {
        $admins = Admin::where('activation_status', '!=', 'activated')->get();
        $count = 0;

        foreach ($admins as $admin) {
            $admin->update([
                'activation_status' => 'activated',
                'status' => 'active',
                'activation_token' => null,
            ]);
            $count++;
        }

        $this->command->info("Successfully activated {$count} admin accounts.");
    }
}
