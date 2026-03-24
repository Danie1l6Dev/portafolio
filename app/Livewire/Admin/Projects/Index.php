<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Enums\ProjectStatus;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public int $perPage = 10;

    protected $queryString = [
        'search',
        'status'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleFeatured(Project $project)
    {
        $project->update([
            'featured' => ! $project->featured
        ]);
    }

    public function render()
    {
        $projects = Project::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%");
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.projects.index', [
            'projects' => $projects,
            'statuses' => ProjectStatus::cases(),
        ])->layout('layouts.app');
    }
}
