<?php

use App\Enums\UserRole;
use App\Livewire\AuthPage;
use App\Livewire\ForgotPasswordPage;
use App\Livewire\ResetPasswordPage;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ProductSeeder::class);
});

it('renders login and registration pages', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Вход в ByClick')
        ->assertSee('Регистрация');

    $this->get('/register')
        ->assertOk()
        ->assertSee('Регистрация в ByClick')
        ->assertSee('Создать аккаунт');
});

it('logs in an existing customer', function () {
    $user = User::factory()->create();

    Livewire::test(AuthPage::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create();

    Livewire::test(AuthPage::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('registers a new customer', function () {
    Livewire::test(AuthPage::class, ['tab' => 'register'])
        ->set('name', 'Иван Петров')
        ->set('registerEmail', 'ivan@example.com')
        ->set('phone', '+375 29 123-45-67')
        ->set('registerPassword', 'secret123')
        ->set('registerPasswordConfirmation', 'secret123')
        ->set('agree', true)
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $user = User::query()->where('email', 'ivan@example.com')->firstOrFail();

    $this->assertAuthenticatedAs($user);

    expect($user->role)->toBe(UserRole::Customer)
        ->and($user->phone)->toBe('+375 29 123-45-67')
        ->and(Hash::check('secret123', $user->password))->toBeTrue();
});

it('validates the registration form', function () {
    Livewire::test(AuthPage::class, ['tab' => 'register'])
        ->call('register')
        ->assertHasErrors([
            'name',
            'registerEmail',
            'registerPassword',
            'registerPasswordConfirmation',
            'agree',
        ]);
});

it('merges guest cart and favorites on login', function () {
    $this->withSession([]);

    $product = Product::query()->where('slug', 'naushniki-aura-pro')->firstOrFail();
    $guestCart = Cart::current();
    $guestCart->add($product, 2);
    $guestSessionId = $guestCart->session_id;
    Favorite::toggle($product);

    $user = User::factory()->create();

    Livewire::test(AuthPage::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors();

    $userCart = Cart::query()->where('user_id', $user->id)->with('items')->first();

    expect($userCart)->not->toBeNull()
        ->and($userCart->items->sum('quantity'))->toBe(2)
        ->and(Favorite::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists())->toBeTrue()
        ->and(Cart::query()->where('session_id', $guestSessionId)->exists())->toBeFalse();
});

it('logs out the current user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('home'));

    $this->assertGuest();
});

it('keeps authenticated users away from auth pages', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect(route('home'));
});

it('sends a password reset notification', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::test(ForgotPasswordPage::class)
        ->set('email', $user->email)
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertSee('отправили ссылку');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('does not reveal unknown e-mails on password reset', function () {
    Notification::fake();

    Livewire::test(ForgotPasswordPage::class)
        ->set('email', 'nobody@example.com')
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertSee('отправили ссылку');

    Notification::assertNothingSent();
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create();
    $token = Password::broker()->createToken($user);

    Livewire::test(ResetPasswordPage::class)
        ->set('token', $token)
        ->set('email', $user->email)
        ->set('password', 'new-secret-123')
        ->set('passwordConfirmation', 'new-secret-123')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);

    expect(Hash::check('new-secret-123', $user->fresh()?->password))->toBeTrue();
});

it('rejects an invalid password reset token', function () {
    $user = User::factory()->create();

    Livewire::test(ResetPasswordPage::class)
        ->set('token', 'invalid-token')
        ->set('email', $user->email)
        ->set('password', 'new-secret-123')
        ->set('passwordConfirmation', 'new-secret-123')
        ->call('resetPassword')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('shows login and logout actions in the header', function () {
    $this->get('/')->assertSee('Войти');

    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertSee('Выйти');
});
