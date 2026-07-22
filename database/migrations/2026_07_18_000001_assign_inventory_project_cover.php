<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const COVER_PATH = 'images/projects/994328cd-03b3-449e-a469-37c7989b5500.png';

    public function up(): void
    {
        DB::table('projects')
            ->where('slug', 'inventario-uniguajira')
            ->whereNull('cover_image')
            ->update([
                'cover_image' => self::COVER_PATH,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('projects')
            ->where('slug', 'inventario-uniguajira')
            ->where('cover_image', self::COVER_PATH)
            ->update([
                'cover_image' => null,
                'updated_at' => now(),
            ]);
    }
};
