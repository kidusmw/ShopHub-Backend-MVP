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
        Schema::create('variant_attribute_option', function (Blueprint $table) {
            $table->foreignId('variant_id')->constrained('variants')->cascadeOnDelete();
            $table->foreignId('attribute_option_id')->constrained('attribute_options')->cascadeOnDelete();
            $table->primary(['variant_id', 'attribute_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_attribute_option');
    }
};
