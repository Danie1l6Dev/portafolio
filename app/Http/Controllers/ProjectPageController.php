<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

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
        $metaImage = $this->coverUrl($project);
        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareSourceCode',
            'name' => $project->title,
            'description' => $project->summary,
            'url' => $canonicalUrl,
            'image' => $metaImage,
            'dateCreated' => $project->started_at?->toDateString(),
            'dateModified' => $project->updated_at?->toDateString(),
            'codeRepository' => $project->repo_url,
            'sameAs' => $project->demo_url,
            'programmingLanguage' => $project->skills->pluck('name')->values()->all(),
            'author' => [
                '@type' => 'Person',
                'name' => config('portfolio.name'),
                'url' => route('home'),
            ],
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        return view('pages.projects.show', [
            'project' => $project,
            'coverUrl' => $metaImage,
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
        if (blank($project->cover_image)) {
            return null;
        }

        $path = ltrim($project->cover_image, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $storageUrl = str_starts_with($path, 'storage/')
            ? '/'.$path
            : Storage::disk('public')->url($path);

        return str_starts_with($storageUrl, 'http://') || str_starts_with($storageUrl, 'https://')
            ? $storageUrl
            : url($storageUrl);
    }
}
