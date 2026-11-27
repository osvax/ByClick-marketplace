<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AuthPage extends Component
{
    /** Active tab: "login" or "register". */
    public string $tab = 'login';

    /** Login form. */
    public string $email = '';

    public string $password = '';

    public bool $remember = true;

    /** Registration form. */
    public string $name = '';

    public string $registerEmail = '';

    public string $phone = '';

    public string $registerPassword = '';

    public string $registerPasswordConfirmation = '';

    public bool $agree = false;

    /**
     * Open the requested tab and keep authenticated users away from the page.
     */
    public function mount(?string $tab = null): void
    {
        $this->tab = in_array($tab, ['login', 'register'], true)
            ? $tab
            : (request()->routeIs('register') ? 'register' : 'login');

        if (auth()->check()) {
            $this->redirect(route('home'), navigate: true);
        }
    }

    /**
     * Switch between the login and registration forms.
     */
    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['login', 'register'], true)) {
            return;
        }

        $this->tab = $tab;
        $this->resetValidation();
    }

    /**
     * Authenticate a customer with rate limiting and merge guest data.
     */
    public function login(): void
    {
        $this->email = trim($this->email);

        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], attributes: [
            'email' => 'e-mail',
            'password' => 'пароль',
        ]);

        $key = 'login:'.Str::lower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Слишком много попыток входа. Попробуйте снова через минуту.',
            ]);
        }

        RateLimiter::hit($key, 60);

        // The guest session id must be captured before the session is regenerated.
        $guestSessionId = session()->getId();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'Неверный e-mail или пароль.',
            ]);
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        RateLimiter::clear($key);

        $this->mergeGuestData($guestSessionId, $user);

        session()->regenerate();

        $this->redirectIntended(route('home'), navigate: true);
    }

    /**
     * Create a customer account, sign in and merge guest data.
     */
    public function register(): void
    {
        $this->name = trim($this->name);
        $this->registerEmail = trim($this->registerEmail);
        $this->phone = trim($this->phone);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'registerEmail' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'phone' => ['nullable', 'string', 'max:32'],
            'registerPassword' => ['required', 'string', 'min:8'],
            'registerPasswordConfirmation' => ['required', 'string', 'same:registerPassword'],
            'agree' => ['accepted'],
        ], attributes: [
            'name' => 'имя',
            'registerEmail' => 'e-mail',
            'phone' => 'телефон',
            'registerPassword' => 'пароль',
            'registerPasswordConfirmation' => 'подтверждение пароля',
            'agree' => 'согласие с условиями',
        ]);

        $guestSessionId = session()->getId();

        $user = User::query()->create([
            'name' => $this->name,
            'email' => $this->registerEmail,
            'phone' => $this->phone !== '' ? $this->phone : null,
            'password' => $this->registerPassword,
            'role' => UserRole::Customer,
        ]);

        Auth::login($user);

        $this->mergeGuestData($guestSessionId, $user);

        session()->regenerate();

        $this->redirectIntended(route('home'), navigate: true);
    }

    /**
     * Demo toast for social login and legal links that are not implemented yet.
     */
    public function demoToast(string $message): void
    {
        $this->dispatch('notify', message: $message);
    }

    /**
     * Move the guest cart and favorites to the freshly authenticated account.
     */
    protected function mergeGuestData(string $sessionId, User $user): void
    {
        Cart::mergeGuestCart($sessionId, $user->id);
        Favorite::mergeGuestFavorites($sessionId, $user->id);
    }

    public function render(): View
    {
        return view('livewire.auth-page');
    }
}
