<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class ResetPasswordPage extends Component
{
    /** Token from the reset link. */
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    /**
     * Read the token and e-mail from the password reset URL.
     */
    public function mount(): void
    {
        $this->token = (string) request()->route('token');
        $this->email = (string) request('email');
    }

    /**
     * Validate the token and replace the password.
     */
    public function resetPassword(): void
    {
        $this->email = trim($this->email);

        $this->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
        ], attributes: [
            'email' => 'e-mail',
            'password' => 'пароль',
            'passwordConfirmation' => 'подтверждение пароля',
        ]);

        $rateKey = 'password-reset:'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            $this->addError('email', 'Слишком много попыток. Запросите новую ссылку позже.');

            return;
        }

        RateLimiter::hit($rateKey, 600);

        $guestSessionId = session()->getId();
        $user = null;

        $status = Password::reset([
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->passwordConfirmation,
            'token' => $this->token,
        ], function (User $resetUser, string $password) use (&$user): void {
            $resetUser->forceFill([
                'password' => $password,
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($resetUser));

            $user = $resetUser;
        });

        if ($status !== Password::PASSWORD_RESET || ! $user instanceof User) {
            $this->addError('email', 'Ссылка недействительна или устарела. Запросите новую.');

            return;
        }

        Auth::login($user);

        // Keep the guest cart/favorites if the reset link was opened in a guest session.
        Cart::mergeGuestCart($guestSessionId, $user->id);
        Favorite::mergeGuestFavorites($guestSessionId, $user->id);

        session()->regenerate();

        $this->redirect(route('home'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.reset-password-page');
    }
}
