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
        Schema::create('generated_characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Basic attributes (Mode 1)
            $table->string('title')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('ethnicity', ['asian', 'black', 'latino', 'white'])->nullable();

            // Advanced attributes (Mode 2)
            $table->enum('eye_color', ['brown', 'gray', 'blue', 'green', 'hazel'])->nullable();
            $table->enum('build', ['slim', 'athletic', 'muscular', 'curvy'])->nullable();
            $table->enum('style', ['casual', 'luxury', 'streetwear', 'futuristic'])->nullable();
            $table->enum('expression', ['confident', 'serious', 'happy', 'futuristic'])->nullable();

            // User refinement prompt
            $table->text('custom_modification')->nullable();

            // AI output
            $table->string('generated_image_path')->nullable();

            // Status tracking — same pattern as projects table
            $table->enum('status', ['pending', 'generating', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_characters');
    }
};
