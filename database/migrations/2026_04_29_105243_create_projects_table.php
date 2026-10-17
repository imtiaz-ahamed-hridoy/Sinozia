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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('target_character_id')->nullable()->constrained('target_characters')->nullOnDelete();
            $table->foreignId('target_voice_id')->nullable()->constrained('target_voices')->nullOnDelete();

            // Files
            $table->string('original_video_path')->nullable();
            $table->string('base_image_path')->nullable();
            $table->string('target_character_path')->nullable();
            $table->string('preview_image_path')->nullable();
            $table->string('final_video_path')->nullable();

            $table->json('transformation_type')->nullable()->comment('e . g . face, body, background');
            $table->longText('llm_generated_prompt')->nullable();
            $table->string('llm_provider_used')->nullable();

            // External job IDs
            $table->string('kling_task_id')->nullable();
            $table->string('elevenlabs_job_id')->nullable();

            // Status tracking
            $table->enum('status', [
                'pending',
                'extracting_frame',
                'generating_prompt',
                'generating_preview',
                'awaiting_user_validation', // for client confirmation before final generation
                'generating_video',
                'processing_voice',
                'completed',
                'failed',
            ])->default('pending');

            $table->boolean('voice_enabled')->default(false);
            $table->string('quality')->default('std')->comment('e.g. std, pro');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('kling_task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
