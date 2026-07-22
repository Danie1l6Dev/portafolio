<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredProjects = Project::query()
            ->published()
            ->featured()
            ->with(['category', 'skills'])
            ->ordered()
            ->get();

        $skills = Skill::query()
            ->ordered()
            ->get()
            ->groupBy(fn (Skill $skill): string => $skill->group ?: 'Otras');

        $experiences = Experience::query()
            ->ordered()
            ->get();

        /** @var list<array{name: string, url: string, icon: string}> $socials */
        $socials = config('portfolio.socials', []);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'mainEntity' => [
                '@type' => 'Person',
                'name' => config('portfolio.name'),
                'jobTitle' => config('portfolio.role'),
                'email' => 'mailto:'.config('portfolio.email'),
                'url' => route('home'),
                'sameAs' => array_column($socials, 'url'),
            ],
        ];

        return view('pages.home', [
            'featuredProjects' => $featuredProjects,
            'skills' => $skills,
            'experiences' => $experiences,
            'schema' => $schema,
        ]);
    }
}
