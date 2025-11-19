<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@xoilac.tv'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );
        
        $this->command->info('Admin user created: admin@xoilac.tv / admin123');
    }
}
