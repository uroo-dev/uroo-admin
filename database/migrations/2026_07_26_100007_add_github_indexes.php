<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->index('language');
            $table->index('last_pushed_at');
            $table->index(['user_id', 'language']);
        });

        Schema::table('commits', function (Blueprint $table) {
            $table->index('committed_at');
            $table->index('branch');
            $table->index(['repository_id', 'committed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->dropIndex(['language']);
            $table->dropIndex(['last_pushed_at']);
            $table->dropIndex(['user_id', 'language']);
        });

        Schema::table('commits', function (Blueprint $table) {
            $table->dropIndex(['committed_at']);
            $table->dropIndex(['branch']);
            $table->dropIndex(['repository_id', 'committed_at']);
        });
    }
};
