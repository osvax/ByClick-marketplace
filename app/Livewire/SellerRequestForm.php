<?php

namespace App\Livewire;

use App\Enums\SellerRequestStatus;
use App\Models\Category;
use App\Models\SellerRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SellerRequestForm extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $categoryId = '';

    public string $comment = '';

    public bool $agree = false;

    /** Show a confirmation screen after a successful request. */
    public bool $submitted = false;

    /**
     * Prefill contact details for authenticated users.
     */
    public function mount(): void
    {
        $user = auth()->user();

        if ($user === null) {
            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    /**
     * Store a seller onboarding request.
     */
    public function submit(): void
    {
        $this->name = trim($this->name);
        $this->phone = trim($this->phone);
        $this->email = trim($this->email);
        $this->categoryId = trim($this->categoryId);
        $this->comment = trim($this->comment);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'categoryId' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where('parent_id', null)
                    ->where('is_active', true),
            ],
            'comment' => ['nullable', 'string', 'max:2000'],
            'agree' => ['accepted'],
        ], attributes: [
            'name' => 'имя',
            'phone' => 'телефон',
            'email' => 'e-mail',
            'categoryId' => 'категория',
            'comment' => 'комментарий',
            'agree' => 'согласие на обработку данных',
        ]);

        SellerRequest::query()->create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'category_id' => (int) $this->categoryId,
            'comment' => $this->comment !== '' ? $this->comment : null,
            'status' => SellerRequestStatus::New,
        ]);

        $this->submitted = true;

        $this->dispatch('notify', message: 'Заявка отправлена — свяжемся с вами');
    }

    public function render(): View
    {
        $categories = Category::query()
            ->roots()
            ->active()
            ->ordered()
            ->get();

        return view('livewire.seller-request-form', [
            'categories' => $categories,
        ]);
    }
}
