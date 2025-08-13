<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\CategorySeeder;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\VendorSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\VariantSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Vendor user
        User::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name' => 'Vendor User',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
            ]
        );

        // Buyer user
        User::firstOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => 'Buyer User',
                'password' => Hash::make('password123'),
                'role' => 'buyer',
            ]
        );

        $this->call([
            CategorySeeder::class,
            AttributeSeeder::class,
            VendorSeeder::class,
            ProductSeeder::class,
            VariantSeeder::class,
        ]);
    }
}
