<?php

namespace Tests\Browser\Loading;

use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\BrowserTestCase;

class IndexTest extends BrowserTestCase
{
    #[Test]
    public function can_see_loading_using_svg(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-loading loading="save" />
                
                    <x-button dusk="save" wire:click="save">Save</x-button>
                </div>
                HTML;
            }

            public function save(): void
            {
                sleep(1);
            }
        })
            ->assertSee('Save')
            ->assertDontSee('svg')
            ->click('@save')
            ->waitUntil('document.querySelector("svg")');
    }

    #[Test]
    public function can_see_loading_using_text(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-loading loading="save">
                        <p dusk="loading">
                            Loading...
                        </p>
                    </x-loading>
                
                    <x-button dusk="save" wire:click="save">Save</x-button>
                </div>
                HTML;
            }

            public function save(): void
            {
                sleep(1);
            }
        })
            ->assertSee('Save')
            ->assertDontSee('svg')
            ->click('@save')
            ->waitForTextIn('@loading', 'Loading...');
    }

    #[Test]
    public function can_see_loading_using_text_with_delay_longest(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-loading loading="save" delay="longest">
                        <p dusk="loading">
                            Loading...
                        </p>
                    </x-loading>
                
                    <x-button dusk="save" wire:click="save">Save</x-button>
                </div>
                HTML;
            }

            public function save(): void
            {
                sleep(4);
            }
        })
            ->assertSee('Save')
            ->assertDontSee('svg')
            ->click('@save')
            ->waitForTextIn('@loading', 'Loading...');
    }
}
