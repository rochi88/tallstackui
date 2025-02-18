<?php

namespace Tests\Browser\Form;

use Livewire\Component;
use Livewire\Livewire;
use Tests\Browser\BrowserTestCase;

class ColorTest extends BrowserTestCase
{
    #[Test]
    public function can_dispatch_event_when_set(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public ?bool $set = false;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    @if ($set)
                        <p dusk="set">{{ $color }}</p>
                    @endif
                    
                    <x-color label="Color" wire:model="color" x-on:set="$wire.set('set', 1)" picker />
                    <x-button dusk="sync" wire:click="sync">Save</x-button>
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->click('@sync')
            ->waitForTextIn('@selected', '#f8fafc')
            ->assertVisible('@set');
    }

    #[Test]
    public function can_open_and_select_color_with_live_entangle(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    <x-color label="Color" wire:model.live="color" />
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->waitForTextIn('@selected', '#64748b')
            ->assertSeeIn('@selected', '#64748b');
    }

    #[Test]
    public function can_open_and_select_first_color(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    <x-color label="Color" wire:model="color" />
                    <x-button dusk="sync" wire:click="sync">Save</x-button>
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->click('@sync')
            ->waitForTextIn('@selected', '#64748b')
            ->assertSeeIn('@selected', '#64748b');
    }

    #[Test]
    public function can_open_and_select_first_color_in_mode_custom(): void
    {
        $this->skipOnGitHubActions();

        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    <x-color label="Color" wire:model="color" :colors="['#FF0000', '#FF5733', '#D7E021']" />
                    
                    <x-button dusk="sync" wire:click="sync">Save</x-button>
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->waitFor('@tallstackui_form_color_floating')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->click('@sync')
            ->waitForTextIn('@selected', '#FF0000')
            ->assertSee('#FF0000')
            ->click('@tallstackui_form_color_open_close')
            ->waitFor('@tallstackui_form_color_floating')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[2]')
            ->click('@sync')
            ->waitForTextIn('@selected', '#FF5733')
            ->assertSee('#FF5733')
            ->click('@tallstackui_form_color_open_close')
            ->waitFor('@tallstackui_form_color_floating')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[3]')
            ->click('@sync')
            ->waitForTextIn('@selected', '#D7E021')
            ->assertSee('#D7E021');
    }

    #[Test]
    public function can_open_and_select_first_color_in_mode_picker(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    <x-color label="Color" wire:model="color" picker />
                    <x-button dusk="sync" wire:click="sync">Save</x-button>
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->click('@sync')
            ->waitForTextIn('@selected', '#f8fafc')
            ->assertSeeIn('@selected', '#f8fafc');
    }

    #[Test]
    public function can_open_and_select_first_color_in_mode_range(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    <x-color label="Color" wire:model="color" />
                    <x-button dusk="sync" wire:click="sync">Save</x-button>
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->dragRight('@tallstackui_form_range', 50)
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->click('@sync')
            ->waitForTextIn('@selected', '#334155')
            ->assertSeeIn('@selected', '#334155');
    }

    #[Test]
    public function can_open_select_a_color_and_clear_it(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    <x-color label="Color" wire:model.live="color" />
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->waitFor('@tallstackui_form_color_floating')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->waitForTextIn('@selected', '#64748b')
            ->click('@tallstackui_form_color_clearable')
            ->waitUntilMissingText('#64748b');
    }

    #[Test]
    public function can_open_select_a_color_and_dispatch_change_event(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    <x-color wire:change="sync" label="Color" wire:model="color" />
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->waitForTextIn('@selected', '#64748b')
            ->assertSeeIn('@selected', '#64748b');
    }

    #[Test]
    public function cannot_see_clearable_when_no_color_is_selected(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $color = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ $color }}</p>
                    
                    <x-color label="Color" wire:model.live="color" />
                </div>
                HTML;
            }

            public function sync(): void
            {
                //
            }
        })
            ->waitForText('Color')
            ->click('@tallstackui_form_color_open_close')
            ->assertMissing('tallstackui_form_color_clearable')
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div/div[2]/button[1]')
            ->waitForTextIn('@selected', '#64748b')
            ->assertVisible('@tallstackui_form_color_clearable')
            ->click('@tallstackui_form_color_clearable')
            ->waitUntilMissingText('#64748b');
    }
}
