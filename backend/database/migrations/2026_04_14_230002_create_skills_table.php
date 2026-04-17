<?php

use App\Enums\SkillGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('group', SkillGroup::values())->nullable()->comment('Grupo permitido de la habilidad');
            $table->unsignedTinyInteger('level')->default(1)->comment('1-5: nivel de dominio');
            $table->string('icon')->nullable()->comment('Nombre de icono o ruta SVG');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
