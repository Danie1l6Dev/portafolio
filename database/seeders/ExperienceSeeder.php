<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        Experience::query()->updateOrCreate(
            [
                'company' => 'Universidad de La Guajira',
                'position' => 'Tutor académico de programación',
            ],
            [
                'location' => 'Maicao, La Guajira',
                'description' => 'Acompaño a estudiantes en lógica de programación y resolución de problemas, reforzando estructuras de control, funciones y estructuras de datos.',
                'started_at' => '2023-09-01',
                'finished_at' => null,
                'is_current' => true,
                'sort_order' => 1,
            ],
        );
    }
}
