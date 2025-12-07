<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('west_news', function (Blueprint $table) {
            $table->unique('url');
        });
    }

    public function down(): void
    {
        Schema::table('west_news', function (Blueprint $table) {
            $table->dropUnique('west_news_url_unique');
        });
    }
};
