<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repository_id')->constrained()->cascadeOnDelete();
            $table->string('sha')->unique();
            $table->text('message');
            $table->string('author_name');
            $table->string('author_email');
            $table->string('branch')->default('main');
            $table->json('modified_files')->nullable();
            $table->json('added_files')->nullable();
            $table->json('deleted_files')->nullable();
            $table->integer('additions')->default(0);
            $table->integer('deletions')->default(0);
            $table->timestamp('committed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commits');
    }
};