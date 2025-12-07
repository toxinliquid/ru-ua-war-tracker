<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('war_posts', function (Blueprint $table) {
            $table->decimal('overall_ukrainian_advance_km2', 10, 2)
                ->nullable()
                ->after('total_ukrainian_gross_km2');
        });
    }

    public function down(): void
    {
        Schema::table('war_posts', function (Blueprint $table) {
            $table->dropColumn('overall_ukrainian_advance_km2');
        });
    }
};
