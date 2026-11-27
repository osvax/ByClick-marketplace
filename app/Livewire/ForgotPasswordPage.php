<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPasswordPage extends Component
{
    public string $email = '';

    /** Status message shown after sending a reset link. */
    public ?string $status = null;

    /**
     * Send a password reset link to the given e-mail.
     *
     * The message is intentionally generic so the page does not reveal
     * whether an account with this e-mail exists.
     */
    public function sendResetLink(): void
    {
        $this->email = trim($this->email);

        $this->validate([
            'email' => ['required', 'string', 'email'],
        ], attributes: [
            'email' => 'e-mail',
        ]);

        Password::sendResetLink(['email' => $this->email]);

        // Keep the message generic so the page does not reveal whether the
        // e-mail belongs to an existing account (or was throttled).
        $this->status = 'Если аккаунт с таким e-mail существует, мы отправили ссылку для сброса пароля.';
    }

    public function render(): View
    {
        return view('livewire.forgot-password-page');
    }
}
