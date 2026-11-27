<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AdminCategories extends Component
{
    public string $search = '';

    /** Inline edit form. */
    public ?int $editingId = null;

    public string $editName = '';

    public string $editSort = '0';

    public bool $editActive = true;

    /** Create form. */
    public bool $showCreate = false;

    public string $newName = '';

    public string $newParentId = '';

    public string $newSort = '0';

    public bool $newActive = true;

    /**
     * Open the edit form.
     */
    public function edit(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);
        Gate::authorize('update', $category);

        $this->editingId = $category->id;
        $this->editName = $category->name;
        $this->editSort = (string) $category->sort_order;
        $this->editActive = $category->is_active;
        $this->showCreate = false;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->resetValidation();
    }

    public function createCategory(): void
    {
        Gate::authorize('viewAny', Category::class);

        $this->showCreate = true;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function cancelCreate(): void
    {
        $this->showCreate = false;
        $this->resetValidation();
    }

    /**
     * Persist the edited category.
     */
    public function save(): void
    {
        if ($this->editingId === null) {
            return;
        }

        $category = Category::query()->findOrFail($this->editingId);
        Gate::authorize('update', $category);

        $this->editName = trim($this->editName);

        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editSort' => ['required', 'integer', 'min:0'],
            'editActive' => ['boolean'],
        ], attributes: [
            'editName' => 'название',
            'editSort' => 'порядок',
        ]);

        $category->update([
            'name' => $this->editName,
            'sort_order' => (int) $this->editSort,
            'is_active' => $this->editActive,
        ]);

        $this->editingId = null;
        $this->dispatch('notify', message: 'Категория обновлена');
    }

    /**
     * Create a new category.
     */
    public function store(): void
    {
        Gate::authorize('viewAny', Category::class);

        $this->newName = trim($this->newName);

        $this->validate([
            'newName' => ['required', 'string', 'max:255'],
            'newParentId' => $this->newParentId === '' ? ['nullable'] : ['required', 'integer', Rule::exists('categories', 'id')],
            'newSort' => ['required', 'integer', 'min:0'],
            'newActive' => ['boolean'],
        ], attributes: [
            'newName' => 'название',
            'newParentId' => 'родительская категория',
            'newSort' => 'порядок',
        ]);

        Category::query()->create([
            'parent_id' => $this->newParentId !== '' ? (int) $this->newParentId : null,
            'name' => $this->newName,
            'slug' => $this->uniqueSlug($this->newName),
            'sort_order' => (int) $this->newSort,
            'is_active' => $this->newActive,
        ]);

        $this->showCreate = false;
        $this->reset('newName', 'newParentId', 'newSort');
        $this->newSort = '0';
        $this->newActive = true;

        $this->dispatch('notify', message: 'Категория создана');
    }

    /**
     * Toggle category visibility in the catalog.
     */
    public function toggleActive(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);
        Gate::authorize('update', $category);

        $category->update(['is_active' => ! $category->is_active]);

        $this->dispatch('notify', message: $category->is_active ? 'Категория включена' : 'Категория скрыта');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Category::class);

        $categories = Category::query()
            ->with('parent')
            ->withCount('products')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.trim($this->search).'%'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.admin-categories', compact('categories'));
    }

    /**
     * Build a unique category slug.
     */
    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'category';
        }

        $candidate = $base;
        $suffix = 1;

        while (Category::query()->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.++$suffix;
        }

        return $candidate;
    }
}
