<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 180);
            $table->string('type', 30)->default('recognition')->index();
            $table->string('organization', 180);
            $table->string('result', 150)->nullable();
            $table->string('role', 180)->nullable();
            $table->text('description')->nullable();
            $table->date('achieved_at');
            $table->string('external_url')->nullable();
            $table->string('image_path')->nullable();
            $table->string('certificate_path')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
