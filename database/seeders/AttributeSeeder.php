<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttributeType;
use App\Models\AttributeOption;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Attribute Types
        $color = AttributeType::create(['name' => 'Color']);
        $size  = AttributeType::create(['name' => 'Size']);

        // Attribute Options
        AttributeOption::insert([
            ['attribute_type_id' => $color->id, 'value' => 'Red'],
            ['attribute_type_id' => $color->id, 'value' => 'Blue'],
            ['attribute_type_id' => $size->id, 'value' => 'S'],
            ['attribute_type_id' => $size->id, 'value' => 'M'],
            ['attribute_type_id' => $size->id, 'value' => 'L'],
        ]);
    }
}
