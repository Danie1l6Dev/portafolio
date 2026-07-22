<?php

namespace App\Livewire\Admin;

use App\Enums\SkillGroup;
use App\Livewire\Admin\Concerns\AuthorizesContentEditors;
use App\Models\Skill;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SkillManager extends Component
{
    use AuthorizesContentEditors;
    use WithPagination;

    public string $search = '';

    public string $groupFilter = '';

    public bool $featuredOnly = false;

    public bool $showForm = false;

    public ?int $editingSkillId = null;

    public string $name = '';

    public string $group = '';

    public int $level = 1;

    public string $icon = '';

    public int $sortOrder = 0;

    public bool $isFeatured = false;

    public bool $confirmingDelete = false;

    public ?int $deletingSkillId = null;

    public string $deletingSkillName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedGroupFilter(): void
    {
        $this->resetPage();
    }

    public function updatedFeaturedOnly(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $skillId): void
    {
        $skill = Skill::findOrFail($skillId);

        $this->editingSkillId = $skill->id;
        $this->name = $skill->name;
        $this->group = $skill->group ?? '';
        $this->level = $skill->level;
        $this->icon = $skill->icon ?? '';
        $this->sortOrder = $skill->sort_order;
        $this->isFeatured = $skill->is_featured;
        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->authorizeContentEditor();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('skills', 'name')->ignore($this->editingSkillId)],
            'group' => ['nullable', Rule::enum(SkillGroup::class)],
            'level' => ['required', 'integer', 'min:1', 'max:5'],
            'icon' => ['nullable', 'string', 'max:255'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isFeatured' => ['boolean'],
        ]);

        $skill = $this->editingSkillId ? Skill::findOrFail($this->editingSkillId) : new Skill;

        $skill->fill([
            'name' => $validated['name'],
            'group' => filled($validated['group']) ? $validated['group'] : null,
            'level' => $validated['level'],
            'icon' => filled($validated['icon']) ? $validated['icon'] : null,
            'sort_order' => $validated['sortOrder'],
            'is_featured' => $validated['isFeatured'],
        ]);

        if (! $skill->exists || $skill->isDirty('name')) {
            $skill->slug = $this->uniqueSlug($skill->name, $skill->exists ? $skill->id : null);
        }

        $skill->save();

        $this->resetForm();
        Flux::toast(variant: 'success', text: 'Habilidad guardada correctamente.');
    }

    public function confirmDelete(int $skillId): void
    {
        $skill = Skill::findOrFail($skillId);

        $this->deletingSkillId = $skill->id;
        $this->deletingSkillName = $skill->name;
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        $this->authorizeContentEditor();

        $skill = Skill::findOrFail($this->deletingSkillId);
        $skill->projects()->detach();
        $skill->delete();

        $this->cancelDelete();
        $this->resetPage();
        Flux::toast(variant: 'success', text: 'Habilidad eliminada.');
    }

    public function cancelDelete(): void
    {
        $this->reset('confirmingDelete', 'deletingSkillId', 'deletingSkillName');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.admin.skill-manager', [
            'skills' => $this->skills(),
            'groups' => SkillGroup::values(),
        ]);
    }

    /** @return LengthAwarePaginator<int, Skill> */
    private function skills(): LengthAwarePaginator
    {
        return Skill::query()
            ->withCount('projects')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->when($this->groupFilter !== '', fn ($query) => $query->inGroup($this->groupFilter))
            ->when($this->featuredOnly, fn ($query) => $query->featured())
            ->ordered()
            ->paginate(12);
    }

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (Skill::query()
            ->where('slug', $slug)
            ->when($excludeId !== null, fn ($query) => $query->whereKeyNot($excludeId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function resetForm(): void
    {
        $this->reset('showForm', 'editingSkillId', 'name', 'group', 'icon', 'sortOrder', 'isFeatured');
        $this->level = 1;
        $this->resetErrorBag();
    }
}
