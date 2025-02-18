<?php

namespace Tests\Browser\Rating;

use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\BrowserTestCase;

class IndexTest extends BrowserTestCase
{
    #[Test]
    public function can_render(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-rating />
                    
                    Rating
                </div>
                HTML;
            }

            public function evaluate(int $quantity): void
            {
                // ...
            }
        })
            ->waitForLivewireToLoad()
            ->assertSee('Rating');
    }

    #[Test]
    public function can_render_slot(): void
    {
        Livewire::visit(new class extends Component
        {
            public string $rating = '';

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="rating">{{ $rating }}</p>

                    <x-rating>
                        FooBar
                    </x-rating>
                </div>
                HTML;
            }

            public function evaluate(string $rating): void
            {
                $this->rating = $rating;
            }
        })->assertSee('FooBar');
    }

    #[Test]
    public function can_use_custom_method(): void
    {
        Livewire::visit(new class extends Component
        {
            public int $rating = 4;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="rating">{{ $rating }}</p>

                    <x-rating wire:model="rating" evaluate-method="fooBar" />
                </div>
                HTML;
            }

            public function fooBar(string $rating): void
            {
                $this->rating = $rating;
            }
        })
            ->assertSeeIn('@rating', '4')
            ->clickAtXPath('/html/body/div[3]/div/button[5]')
            ->waitForTextIn('@rating', '5')
            ->assertSeeIn('@rating', '5')
            ->assertSee('5');
    }

    #[Test]
    public function can_use_events(): void
    {
        Livewire::visit(new class extends Component
        {
            public int $rating = 3;

            public ?string $rated = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($rated)
                        <p dusk="rated">{{ $rated }}</p>
                    @endif

                    <p dusk="rating">{{ $rating }}</p>

                    <x-rating wire:model="rating" x-on:evaluate="$wire.set('rated', 'rated')" />
                </div>
                HTML;
            }

            public function evaluate(string $rating): void
            {
                $this->rating = $rating;
            }
        })
            ->assertSeeIn('@rating', '3')
            ->clickAtXPath('/html/body/div[3]/div/button[4]')
            ->waitForTextIn('@rating', '4')
            ->assertSeeIn('@rating', '4')
            ->assertVisible('@rated')
            ->assertSeeIn('@rated', 'rated');
    }

    #[Test]
    public function cannot_allow_usage_of_float(): void
    {
        Livewire::visit(new class extends Component
        {
            public float $rating = 3.5;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-rating wire:model="rating" />
                </div>
                HTML;
            }

            public function evaluate(string $rating): void
            {
                $this->rating = $rating;
            }
        })
            ->assertSee('The rating [value] must be a int.');
    }

    #[Test]
    public function cannot_rating_when_static(): void
    {
        Livewire::visit(new class extends Component
        {
            public int $rating = 3;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="rating">{{ $rating }}</p>

                    <x-rating static />
                </div>
                HTML;
            }

            public function evaluate(string $rating): void
            {
                $this->rating = $rating;
            }
        })
            ->assertSeeIn('@rating', '3')
            ->clickAtXPath('/html/body/div[3]/div/button[4]')
            ->waitForTextIn('@rating', '3')
            ->assertSeeIn('@rating', '3')
            ->assertSee('3');
    }

    #[Test]
    public function does_not_need_to_use_rate_with_livewire_context(): void
    {
        Livewire::visit(new class extends Component
        {
            public int $rating = 3;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-rating wire:model="rating" :rate="$rating" />
                </div>
                HTML;
            }

            public function evaluate(string $rating): void
            {
                $this->rating = $rating;
            }
        })
            ->assertSee('The rating [rate] can be omitted because you are in Livewire context. You can use `wire:model` instead.');
    }
}
