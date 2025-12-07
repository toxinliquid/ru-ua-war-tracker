<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('war_posts', function (Blueprint $table) {
            $table->id();

            // Optional human title like: "Russian and Ukrainian advances from Day 1345–1346"
            $table->string('title')->nullable();

            // Calendar range of the update post
            $table->date('date_from');
            $table->date('date_to');

            // Optional "war day number" range (if you want to display “Day 1345–1346”)
            $table->unsignedInteger('day_from')->nullable();
            $table->unsignedInteger('day_to')->nullable();

            // Rich description of the post
            $table->longText('description')->nullable();

            // Cached totals (km^2). We’ll keep them in sync on save; also computable on the fly.
            $table->decimal('total_russian_gross_km2', 10, 2)->default(0);
            $table->decimal('total_ukrainian_gross_km2', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('war_posts');
    }
};
