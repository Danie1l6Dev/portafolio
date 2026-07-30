<?php

namespace App\Livewire\Admin;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $totalProjects = Project::count();
        $projectCounts = Project::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $publishedProjects = (int) ($projectCounts['published'] ?? 0);
        $draftProjects = (int) ($projectCounts['draft'] ?? 0);

        return view('livewire.admin.dashboard', [
            'stats' => [
                'projects' => $totalProjects,
                'published' => $publishedProjects,
                'drafts' => $draftProjects,
                'featured' => Project::featured()->count(),
                'skills' => Skill::count(),
                'categories' => Category::count(),
                'experiences' => Experience::count(),
                'achievements' => Achievement::count(),
                'unreadMessages' => Message::unread()->count(),
                'publicationRate' => $totalProjects > 0 ? (int) round(($publishedProjects / $totalProjects) * 100) : 0,
                'contentItems' => $totalProjects + Experience::count() + Achievement::count(),
            ],
            'recentProjects' => Project::query()
                ->with('category')
                ->latest('updated_at')
                ->limit(5)
                ->get(),
            'recentMessages' => Message::query()
                ->latestFirst()
                ->limit(5)
                ->get(),
        ]);
    }
}
