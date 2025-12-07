<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('war_post_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('war_post_id')->constrained()->cascadeOnDelete();

            // Image paths (stored via Livewire upload)
            $table->string('image_path')->nullable(); // storage path
            $table->string('image_alt')->nullable();  // accessibility/alt

            // Who made the advance for this item
            $table->enum('side', ['russia', 'ukraine', 'neutral'])->default('neutral');

            // Short + long descriptions shown under the picture
            $table->string('short_description')->nullable(); // e.g., "Advance = 9.94km²"
            $table->longText('long_description')->nullable();

            // Numeric advance amount for tracking and comparing (km^2)
            $table->decimal('advance_km2', 10, 2)->nullable();

            // For manual ordering of pictures under the post
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('war_post_items');
    }
};
