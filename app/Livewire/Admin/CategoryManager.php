<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\AuthorizesContentEditors;
use App\Models\Category;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use AuthorizesContentEditors;
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingCategoryId = null;

    public string $name = '';

    public string $description = '';

    public string $color = '';

    public int $sortOrder = 0;

    public bool $confirmingDelete = false;

    public ?int $deletingCategoryId = null;

    public string $deletingCategoryName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->color = $category->color ?? '';
        $this->sortOrder = $category->sort_order;
        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->authorizeContentEditor();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($this->editingCategoryId)],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $category = $this->editingCategoryId
            ? Category::findOrFail($this->editingCategoryId)
            : new Category;

        $category->fill([
            'name' => $validated['name'],
            'description' => filled($validated['description']) ? $validated['description'] : null,
            'color' => filled($validated['color']) ? strtoupper($validated['color']) : null,
            'sort_order' => $validated['sortOrder'],
        ]);

        if (! $category->exists || $category->isDirty('name')) {
            $category->slug = $category->generateSlug($category->name, $category->exists ? $category->id : null);
        }

        $category->save();

        $this->resetForm();
        Flux::toast(variant: 'success', text: 'Categoría guardada correctamente.');
    }

    public function confirmDelete(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->confirmingDelete = true;
        $this->resetErrorBag('delete');
    }

    public function delete(): void
    {
        $this->authorizeContentEditor();

        $category = Category::findOrFail($this->deletingCategoryId);
        $projectsCount = $category->projects()->count();

        if ($projectsCount > 0) {
            $this->addError('delete', "Reasigna primero los {$projectsCount} proyecto(s) asociados.");

            return;
        }

        $category->delete();
        $this->cancelDelete();
        $this->resetPage();
        Flux::toast(variant: 'success', text: 'Categoría eliminada.');
    }

    public function cancelDelete(): void
    {
        $this->reset('confirmingDelete', 'deletingCategoryId', 'deletingCategoryName');
        $this->resetErrorBag('delete');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.admin.category-manager', [
            'categories' => $this->categories(),
        ]);
    }

    /** @return LengthAwarePaginator<int, Category> */
    private function categories(): LengthAwarePaginator
    {
        return Category::query()
            ->withCount('projects')
            ->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            }))
            ->ordered()
            ->paginate(10);
    }

    private function resetForm(): void
    {
        $this->reset('showForm', 'editingCategoryId', 'name', 'description', 'color', 'sortOrder');
        $this->resetErrorBag();
    }
}
