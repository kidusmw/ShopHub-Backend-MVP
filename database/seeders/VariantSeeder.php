<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Variant;
use App\Models\AttributeOption;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example: T-Shirt Variants
        $variant1 = Variant::create([
            'product_id' => 1,
            'sku' => 'TSHIRT-RED-S',
            'stock' => 50,
            'price' => 20.00,
            'status' => 'available',
            'name' => 'Red T-Shirt - Small'
        ]);

        $variant2 = Variant::create([
            'product_id' => 1,
            'sku' => 'TSHIRT-BLU-M',
            'stock' => 30,
            'price' => 22.00,
            'status' => 'available',
            'name' => 'Blue T-Shirt - Medium'
        ]);

        // Link Variants to Attribute Options (pivot table)
        $red = AttributeOption::where('value', 'Red')->first()->id;
        $blue = AttributeOption::where('value', 'Blue')->first()->id;
        $small = AttributeOption::where('value', 'S')->first()->id;
        $medium = AttributeOption::where('value', 'M')->first()->id;

        $variant1->attributeOptions()->attach([$red, $small]);
        $variant2->attributeOptions()->attach([$blue, $medium]);
    }
}
