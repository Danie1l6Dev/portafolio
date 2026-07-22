<?php

namespace App\Livewire\Admin;

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
        $projectCounts = Project::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('livewire.admin.dashboard', [
            'stats' => [
                'projects' => Project::count(),
                'published' => (int) ($projectCounts['published'] ?? 0),
                'drafts' => (int) ($projectCounts['draft'] ?? 0),
                'featured' => Project::featured()->count(),
                'skills' => Skill::count(),
                'categories' => Category::count(),
                'experiences' => Experience::count(),
                'unreadMessages' => Message::unread()->count(),
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
