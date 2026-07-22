<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $projects = Project::query()
            ->published()
            ->ordered()
            ->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap', ['projects' => $projects])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        return response(
            "User-agent: *\nDisallow:\n\nSitemap: ".route('sitemap')."\n",
            200,
            ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }
}
