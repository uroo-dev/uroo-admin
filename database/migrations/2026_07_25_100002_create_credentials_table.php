<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // hosting, vps, ssh, database, cpanel, cloud, ftp, api_key, email
            $table->string('label');
            $table->string('provider')->nullable();
            $table->string('domain')->nullable();
            $table->string('host_ip')->nullable();
            $table->string('username')->nullable();
            $table->text('password_encrypted')->nullable();
            $table->string('database_name')->nullable();
            $table->string('database_user')->nullable();
            $table->text('database_password_encrypted')->nullable();
            $table->text('ssh_key_encrypted')->nullable();
            $table->string('auth_url')->nullable();
            $table->text('notes')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};