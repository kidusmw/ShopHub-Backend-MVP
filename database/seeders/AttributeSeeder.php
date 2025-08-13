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
        $material = AttributeType::create(['name' => 'Material']);
        $style = AttributeType::create(['name' => 'Style']);
        $pattern = AttributeType::create(['name' => 'Pattern']);
        $fit = AttributeType::create(['name' => 'Fit']);

        // Attribute Options
        AttributeOption::insert([
            // Colors
            ['attribute_type_id' => $color->id, 'value' => 'Red'],
            ['attribute_type_id' => $color->id, 'value' => 'Blue'],
            ['attribute_type_id' => $color->id, 'value' => 'Green'],
            ['attribute_type_id' => $color->id, 'value' => 'Black'],
            ['attribute_type_id' => $color->id, 'value' => 'White'],

            // Sizes
            ['attribute_type_id' => $size->id, 'value' => 'XS'],
            ['attribute_type_id' => $size->id, 'value' => 'S'],
            ['attribute_type_id' => $size->id, 'value' => 'M'],
            ['attribute_type_id' => $size->id, 'value' => 'L'],
            ['attribute_type_id' => $size->id, 'value' => 'XL'],

            // Materials
            ['attribute_type_id' => $material->id, 'value' => 'Cotton'],
            ['attribute_type_id' => $material->id, 'value' => 'Wool'],
            ['attribute_type_id' => $material->id, 'value' => 'Polyester'],
            ['attribute_type_id' => $material->id, 'value' => 'Leather'],

            // Styles
            ['attribute_type_id' => $style->id, 'value' => 'Casual'],
            ['attribute_type_id' => $style->id, 'value' => 'Formal'],
            ['attribute_type_id' => $style->id, 'value' => 'Sport'],
            ['attribute_type_id' => $style->id, 'value' => 'Vintage'],

            // Patterns
            ['attribute_type_id' => $pattern->id, 'value' => 'Solid'],
            ['attribute_type_id' => $pattern->id, 'value' => 'Striped'],
            ['attribute_type_id' => $pattern->id, 'value' => 'Checked'],
            ['attribute_type_id' => $pattern->id, 'value' => 'Printed'],

            // Fit
            ['attribute_type_id' => $fit->id, 'value' => 'Slim'],
            ['attribute_type_id' => $fit->id, 'value' => 'Regular'],
            ['attribute_type_id' => $fit->id, 'value' => 'Loose'],
        ]);
    }
}
