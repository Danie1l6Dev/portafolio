<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Web',
                'slug'        => 'web',
                'description' => 'Aplicaciones y sistemas web completos.',
                'color'       => '#3B82F6',
                'sort_order'  => 1,
            ],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['slug' => $data['slug']],
                $data,
            );
        }
    }
}
