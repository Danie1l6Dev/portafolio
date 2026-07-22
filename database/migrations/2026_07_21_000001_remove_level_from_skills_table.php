<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table): void {
            $table->dropColumn('level');
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table): void {
            $table->unsignedTinyInteger('level')->default(1)->comment('1-5: nivel de dominio');
        });
    }
};
