<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('supabase_uid')->nullable()->unique()->after('email');
        });

        Schema::create('supabase_sync_watermarks', function (Blueprint $table) {
            $table->string('table_name')->primary();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supabase_sync_watermarks');
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['supabase_uid']);
            $table->dropColumn('supabase_uid');
        });
    }
};
