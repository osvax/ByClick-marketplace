<?php

namespace App\Livewire;

use App\Enums\PromoType;
use App\Models\PromoCode;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class AdminPromoCodes extends Component
{
    use WithPagination;

    public string $search = '';

    /** Create form. */
    public bool $showCreate = false;

    public string $code = '';

    public string $type = 'percent';

    public string $value = '';

    public string $minTotal = '0';

    public string $usageLimit = '';

    public string $endsAt = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function createPromoCode(): void
    {
        Gate::authorize('create', PromoCode::class);

        $this->showCreate = true;
        $this->resetValidation();
    }

    public function cancelCreate(): void
    {
        $this->showCreate = false;
        $this->resetValidation();
    }

    /**
     * Create a new promo code.
     */
    public function store(): void
    {
        Gate::authorize('create', PromoCode::class);

        $this->code = mb_strtoupper(trim($this->code));

        $rules = [
            'code' => ['required', 'string', 'max:32', Rule::unique('promo_codes', 'code')],
            'type' => ['required', Rule::enum(PromoType::class)],
            'value' => ['required', 'numeric', 'min:0'],
            'minTotal' => ['nullable', 'numeric', 'min:0'],
            'usageLimit' => $this->usageLimit === '' ? ['nullable'] : ['required', 'integer', 'min:1'],
            'endsAt' => $this->endsAt === '' ? ['nullable'] : ['required', 'date', 'after:now'],
        ];

        if ($this->type === PromoType::Percent->value) {
            $rules['value'][] = 'max:100';
        }

        $this->validate($rules, attributes: [
            'code' => 'код',
            'type' => 'тип',
            'value' => 'значение',
            'minTotal' => 'минимальная сумма',
            'usageLimit' => 'лимит использований',
            'endsAt' => 'дата окончания',
        ]);

        $promoType = PromoType::from($this->type);

        PromoCode::query()->create([
            'code' => $this->code,
            'type' => $promoType,
            'value' => $promoType === PromoType::Percent
                ? (int) $this->value
                : $this->parseMoney($this->value),
            'min_total' => $this->minTotal !== '' ? $this->parseMoney($this->minTotal) : 0,
            'starts_at' => null,
            'ends_at' => $this->endsAt !== '' ? CarbonImmutable::parse($this->endsAt)->endOfDay() : null,
            'usage_limit' => $this->usageLimit !== '' ? (int) $this->usageLimit : null,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $this->showCreate = false;
        $this->reset('code', 'type', 'value', 'minTotal', 'usageLimit', 'endsAt');
        $this->type = PromoType::Percent->value;
        $this->minTotal = '0';

        $this->dispatch('notify', message: 'Промокод создан');
    }

    /**
     * Enable or disable a promo code.
     */
    public function toggleActive(int $promoCodeId): void
    {
        $promoCode = PromoCode::query()->findOrFail($promoCodeId);
        Gate::authorize('update', $promoCode);

        $promoCode->update(['is_active' => ! $promoCode->is_active]);

        $this->dispatch('notify', message: $promoCode->is_active ? 'Промокод включён' : 'Промокод выключен');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', PromoCode::class);

        $promoCodes = PromoCode::query()
            ->when($this->search !== '', fn ($query) => $query->where('code', 'like', '%'.mb_strtoupper(trim($this->search)).'%'))
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin-promo-codes', compact('promoCodes'));
    }

    /**
     * Convert a ruble input into kopecks.
     */
    protected function parseMoney(string $value): int
    {
        return (int) round((float) str_replace(',', '.', $value) * 100);
    }
}
