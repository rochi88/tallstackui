<?php

namespace Tests\Browser\Form;

use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\BrowserTestCase;

class RangeTest extends BrowserTestCase
{
    #[Test]
    public function can_increase(): void
    {
        $this->skipOnGitHubActions();

        Livewire::visit(new class extends Component
        {
            public int $quantity = 0;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="increased">{{ $quantity }}</p>
                    
                    <x-range wire:model="quantity" />
                    
                    <x-button dusk="sync" wire:click="sync">Save</x-button>
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->dragRight('@tallstackui_form_range_input', 20)
            ->click('@sync')
            ->waitForTextIn('@increased', '52', 10)
            ->assertSeeIn('@increased', '52');
    }

    #[Test]
    public function can_increase_with_live_entangle(): void
    {
        $this->skipOnGitHubActions();

        Livewire::visit(new class extends Component
        {
            public int $quantity = 0;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="increased">{{ $quantity }}</p>
                    
                    <x-range wire:model.live="quantity" />
                </div>
                HTML;
            }
        })
            ->dragRight('@tallstackui_form_range_input', 20)
            ->waitForTextIn('@increased', '52', 10)
            ->assertSeeIn('@increased', '52');
    }
}
