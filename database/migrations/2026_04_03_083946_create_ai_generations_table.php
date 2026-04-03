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
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slide_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('openai');
            $table->string('model')->default('gpt-4o');
            $table->text('prompt_sent')->nullable();
            $table->json('schema_sent')->nullable();
            $table->json('response_raw')->nullable();
            $table->json('response_parsed')->nullable();
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->integer('attempt')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_generations');
    }
};
