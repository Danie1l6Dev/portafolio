<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary')->comment('Descripción corta para tarjetas');
            $table->longText('description')->nullable()->comment('Contenido completo, acepta Markdown');
            $table->string('demo_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->string('cover_image')->nullable()->comment('Ruta relativa al disco público');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->date('started_at')->nullable();
            $table->date('finished_at')->nullable()->comment('Null si es un proyecto en curso');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
