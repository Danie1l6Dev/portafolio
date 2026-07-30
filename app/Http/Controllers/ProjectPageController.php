<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;

class ProjectPageController extends Controller
{
    public function __invoke(Project $project): View
    {
        abort_unless($project->isPublished(), 404);

        $project->loadMissing([
            'category:id,name,slug,color',
            'skills' => static fn ($query) => $query
                ->select(['skills.id', 'skills.name', 'skills.slug', 'skills.group', 'skills.icon', 'skills.sort_order'])
                ->ordered(),
            'media' => static fn ($query) => $query
                ->where('collection', 'gallery')
                ->orderBy('sort_order'),
        ]);

        $canonicalUrl = route('portfolio.projects.show', ['project' => $project->slug]);
        $coverUrl = $this->coverUrl($project);
        $metaImage = $coverUrl ?? asset(config('portfolio.seo.default_image'));
        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $project->title,
            'description' => $project->summary,
            'url' => $canonicalUrl,
            'image' => $metaImage,
            'dateCreated' => $project->started_at?->toDateString(),
            'dateModified' => $project->updated_at?->toDateString(),
            'applicationCategory' => 'WebApplication',
            'operatingSystem' => 'Web',
            'sameAs' => $project->demo_url,
            'author' => [
                '@type' => 'Person',
                'name' => config('portfolio.name'),
                'url' => route('home'),
            ],
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        return view('pages.projects.show', [
            'project' => $project,
            'coverUrl' => $coverUrl,
            'title' => $project->title,
            'description' => $project->summary,
            'canonical' => $canonicalUrl,
            'ogImage' => $metaImage,
            'metaTitle' => $project->title.' — Daniel Sierra',
            'metaDescription' => $project->summary,
            'canonicalUrl' => $canonicalUrl,
            'metaImage' => $metaImage,
            'schema' => $schema,
        ]);
    }

    private function coverUrl(Project $project): ?string
    {
        return $project->coverUrl();
    }
}
