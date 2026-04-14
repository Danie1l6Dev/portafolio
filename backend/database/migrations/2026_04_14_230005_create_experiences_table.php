<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('position');
            $table->string('location')->nullable();
            $table->text('description')->nullable()->comment('Responsabilidades y logros, acepta Markdown');
            $table->string('company_url')->nullable();
            $table->string('company_logo')->nullable()->comment('Ruta relativa al disco público');
            $table->date('started_at');
            $table->date('finished_at')->nullable()->comment('Null si es el empleo actual');
            $table->boolean('is_current')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
