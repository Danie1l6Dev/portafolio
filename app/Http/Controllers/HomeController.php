<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
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

        $achievements = Achievement::query()
            ->visible()
            ->with(['media' => fn ($query) => $query->where('collection', 'gallery')])
            ->ordered()
            ->get();

        $achievementSectionIndex = 4 + (int) $experiences->isNotEmpty();
        $contactSectionIndex = $achievementSectionIndex + (int) $achievements->isNotEmpty();

        /** @var list<array{name: string, url: string, icon: string}> $socials */
        $socials = config('portfolio.socials', []);
        $profileUrl = route('home');
        $personId = $profileUrl.'#daniel-sierra';
        $awards = $achievements
            ->map(fn (Achievement $achievement): string => $achievement->result
                ? "{$achievement->result} — {$achievement->title}"
                : $achievement->title)
            ->values()
            ->all();
        $technologies = $skills
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => $profileUrl.'#website',
                    'name' => config('portfolio.name'),
                    'url' => $profileUrl,
                    'inLanguage' => 'es-CO',
                ],
                [
                    '@type' => 'ProfilePage',
                    '@id' => $profileUrl.'#profile',
                    'url' => $profileUrl,
                    'name' => config('portfolio.name').' — '.config('portfolio.role'),
                    'inLanguage' => 'es-CO',
                    'isPartOf' => ['@id' => $profileUrl.'#website'],
                    'mainEntity' => ['@id' => $personId],
                ],
                [
                    '@type' => 'Person',
                    '@id' => $personId,
                    'name' => config('portfolio.name'),
                    'jobTitle' => config('portfolio.role'),
                    'email' => 'mailto:'.config('portfolio.email'),
                    'url' => $profileUrl,
                    'image' => asset(config('portfolio.seo.default_image')),
                    'sameAs' => array_column($socials, 'url'),
                    'knowsAbout' => $technologies,
                    'award' => $awards,
                    'affiliation' => [
                        '@type' => 'CollegeOrUniversity',
                        'name' => config('portfolio.education.institution'),
                    ],
                ],
            ],
        ];

        return view('pages.home', [
            'featuredProjects' => $featuredProjects,
            'skills' => $skills,
            'experiences' => $experiences,
            'achievements' => $achievements,
            'achievementSectionIndex' => str_pad((string) $achievementSectionIndex, 2, '0', STR_PAD_LEFT),
            'contactSectionIndex' => str_pad((string) $contactSectionIndex, 2, '0', STR_PAD_LEFT),
            'schema' => $schema,
        ]);
    }
}
