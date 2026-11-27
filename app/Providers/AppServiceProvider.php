<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Favorite;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerViewComposers();
    }

    /**
     * Share the catalog navigation with the site header and mobile menu.
     */
    protected function registerViewComposers(): void
    {
        $loadCategories = fn () => Category::query()
            ->roots()
            ->active()
            ->ordered()
            ->with(['children' => fn (HasMany $query) => $query->where('is_active', true)])
            ->get();

        $loadCartCount = fn (): int => Cart::currentCart()?->totalQuantity() ?? 0;
        $loadFavoritesCount = fn (): int => Favorite::countForCurrent();
        $loadFavoriteIds = fn (): array => Favorite::idsForCurrent();

        View::composer(['components.site.header', 'components.site.menu'], function (\Illuminate\View\View $view) use ($loadCategories, $loadCartCount, $loadFavoritesCount): void {
            $view->with([
                'categories' => once($loadCategories),
                'cartCount' => once($loadCartCount),
                'favoritesCount' => once($loadFavoritesCount),
            ]);
        });

        // Product cards highlight the heart for products in the current favorites.
        View::composer('components.site.product-card', function (\Illuminate\View\View $view) use ($loadFavoriteIds): void {
            $view->with('favoriteProductIds', once($loadFavoriteIds));
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
