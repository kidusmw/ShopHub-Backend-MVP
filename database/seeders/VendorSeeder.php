<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Vendor One',
                'email' => 'vendor1@example.com',
                'password' => bcrypt('password'),
                'role' => 'vendor'
            ],
            [
                'name' => 'Vendor Two',
                'email' => 'vendor2@example.com',
                'password' => bcrypt('password'),
                'role' => 'vendor'
            ],
        ]);
    }
}
