<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('total_trades')->default(0)->after('status');
            $table->unsignedInteger('positive_reviews')->default(0)->after('total_trades');
            $table->unsignedInteger('negative_reviews')->default(0)->after('positive_reviews');
            $table->unsignedInteger('late_count')->default(0)->after('negative_reviews');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'total_trades', 'positive_reviews', 'negative_reviews', 'late_count'
            ]);
        });
    }
};