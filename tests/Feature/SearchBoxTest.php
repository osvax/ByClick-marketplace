<?php

use App\Livewire\SearchBox;
use Database\Seeders\ProductSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ProductSeeder::class);
});

it('does not show suggestions for a short query', function () {
    Livewire::test(SearchBox::class)
        ->set('query', 'н')
        ->assertSet('open', false)
        ->assertDontSee('va_search__suggest', false);
});

it('shows matching products while typing', function () {
    Livewire::test(SearchBox::class)
        ->set('query', 'науш')
        ->assertSet('open', true)
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertSee('49,90 BYN')
        ->assertSee('Показать все результаты')
        ->assertDontSee('Кружка керамическая', false);
});

it('finds products by brand', function () {
    Livewire::test(SearchBox::class)
        ->set('query', 'Aura')
        ->assertSee('Наушники «Аура Pro»', false);
});

it('shows an empty state when nothing matches', function () {
    Livewire::test(SearchBox::class)
        ->set('query', 'несуществующий товар')
        ->assertSet('open', true)
        ->assertSee('Ничего не нашлось');
});

it('closes and reopens the dropdown', function () {
    Livewire::test(SearchBox::class)
        ->set('query', 'науш')
        ->assertSet('open', true)
        ->call('closeSuggestions')
        ->assertSet('open', false)
        ->call('openSuggestions')
        ->assertSet('open', true);
});

it('redirects to the catalog with the search query', function () {
    Livewire::test(SearchBox::class)
        ->set('query', 'наушники')
        ->call('search')
        ->assertRedirect(route('catalog', ['q' => 'наушники']));
});

it('redirects to the plain catalog for an empty query', function () {
    Livewire::test(SearchBox::class)
        ->call('search')
        ->assertRedirect(route('catalog'));
});

it('filters the catalog by the submitted query', function () {
    $this->get('/catalog?q=наушники')
        ->assertOk()
        ->assertSee('Наушники «Аура Pro»', false)
        ->assertDontSee('Кружка керамическая', false);
});
