<?php

namespace App\Livewire\Portfolio;

use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portfolio')]
#[Title('Proyectos')]
class ProjectBrowser extends Component
{
    use WithPagination;

    private const PER_PAGE = 9;

    #[Url(as: 'buscar', history: true, except: '')]
    public string $search = '';

    #[Url(as: 'categoria', history: true, except: '')]
    public string $category = '';

    #[Url(as: 'tecnologia', history: true, except: '')]
    public string $technology = '';

    public function updatedSearch(): void
    {
        $this->search = mb_substr($this->search, 0, 80);
        $this->resetProjectPage();
    }

    public function updatedCategory(): void
    {
        $this->resetProjectPage();
    }

    public function updatedTechnology(): void
    {
        $this->resetProjectPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'category', 'technology');
        $this->resetProjectPage();
    }

    /** @return Collection<int, Category> */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->whereHas('projects', static function (Builder $query): void {
                $query->where('projects.status', 'published');
            })
            ->ordered()
            ->get(['id', 'name', 'slug']);
    }

    /** @return Collection<int, Skill> */
    #[Computed]
    public function technologies(): Collection
    {
        return Skill::query()
            ->whereHas('projects', static function (Builder $query): void {
                $query->where('projects.status', 'published');
            })
            ->ordered()
            ->get(['id', 'name', 'slug', 'group']);
    }

    public function render(): View
    {
        $term = trim($this->search);

        $projects = Project::query()
            ->published()
            ->with([
                'category:id,name,slug,color',
                'skills' => static fn ($query) => $query
                    ->select(['skills.id', 'skills.name', 'skills.slug', 'skills.group', 'skills.icon', 'skills.sort_order'])
                    ->ordered(),
            ])
            ->when($term !== '', static function (Builder $query) use ($term): void {
                $query->where(static function (Builder $searchQuery) use ($term): void {
                    $searchQuery
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('summary', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($this->category !== '', function (Builder $query): void {
                $query->whereHas('category', function (Builder $categoryQuery): void {
                    $categoryQuery->where('slug', $this->category);
                });
            })
            ->when($this->technology !== '', function (Builder $query): void {
                $query->whereHas('skills', function (Builder $skillQuery): void {
                    $skillQuery->where('skills.slug', $this->technology);
                });
            })
            ->ordered()
            ->paginate(self::PER_PAGE, pageName: 'pagina');

        return view('livewire.portfolio.project-browser', [
            'projects' => $projects,
        ]);
    }

    private function resetProjectPage(): void
    {
        $this->resetPage(pageName: 'pagina');
    }
}
