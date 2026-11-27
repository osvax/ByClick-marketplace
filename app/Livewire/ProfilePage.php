<?php

namespace App\Livewire;

use App\Enums\OrderStatus;
use App\Livewire\Concerns\RepeatsOrders;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProfilePage extends Component
{
    use RepeatsOrders;

    /** Contact details form. */
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    /** Password change form. */
    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    /**
     * Fill the forms with the current customer data.
     */
    public function mount(): void
    {
        $user = $this->currentUser();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    /**
     * Save name, e-mail and phone.
     */
    public function updateProfile(): void
    {
        $user = $this->currentUser();

        $this->name = trim($this->name);
        $this->email = trim($this->email);
        $this->phone = trim($this->phone);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:32'],
        ], attributes: [
            'name' => 'имя',
            'email' => 'e-mail',
            'phone' => 'телефон',
        ]);

        $user->forceFill([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone !== '' ? $this->phone : null,
        ])->save();

        $this->dispatch('notify', message: 'Профиль обновлён');
    }

    /**
     * Change the password after checking the current one.
     */
    public function updatePassword(): void
    {
        $user = $this->currentUser();

        $this->validate([
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:8'],
            'newPasswordConfirmation' => ['required', 'string', 'same:newPassword'],
        ], attributes: [
            'currentPassword' => 'текущий пароль',
            'newPassword' => 'новый пароль',
            'newPasswordConfirmation' => 'подтверждение пароля',
        ]);

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Текущий пароль неверен.');

            return;
        }

        $user->forceFill([
            'password' => $this->newPassword,
            'remember_token' => Str::random(60),
        ])->save();

        $this->reset('currentPassword', 'newPassword', 'newPasswordConfirmation');
        $this->resetValidation();

        $this->dispatch('notify', message: 'Пароль обновлён');
    }

    public function render(): View
    {
        $user = $this->currentUser();

        $ordersQuery = Order::query()->where('user_id', $user->id);
        $ordersCount = (clone $ordersQuery)->count();
        $paidOrdersCount = (clone $ordersQuery)->where('status', '!=', OrderStatus::Cancelled)->count();
        $spent = (int) (clone $ordersQuery)->where('status', '!=', OrderStatus::Cancelled)->sum('total');

        /** @var Collection<int, Order> $recentOrders */
        $recentOrders = (clone $ordersQuery)
            ->with(['items.product.mainImage', 'items.product.category'])
            ->latest('created_at')
            ->limit(3)
            ->get();

        $stats = [
            'orders' => $ordersCount,
            'spent' => $spent,
            'average' => $paidOrdersCount > 0 ? intdiv($spent, $paidOrdersCount) : 0,
            'favorites' => Favorite::countForCurrent(),
            'cart' => Cart::currentCart()?->totalQuantity() ?? 0,
        ];

        return view('livewire.profile-page', [
            'user' => $user,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'lastOrder' => $recentOrders->first(),
        ]);
    }

    /**
     * The page is protected by the auth middleware; this narrows the type.
     */
    protected function currentUser(): User
    {
        $user = auth()->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
}
