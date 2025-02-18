<?php

namespace Tests\Browser\Tab;

use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\BrowserTestCase;

class IndexTest extends BrowserTestCase
{
    #[Test]
    public function can_dispatch_event(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $selected = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <p dusk="selected">{{ $selected }}</p>

                    <x-tab selected="Foo" x-on:navigate="$wire.set('selected', $event.detail.select)">
                        <x-tab.items tab="Foo">
                            Foo bar baz
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Foo bar baz')
            ->assertDontSee('Baz bar foo')
            ->clickAtXPath('/html/body/div[3]/div/ul/li[2]')
            ->waitForText('Baz bar foo')
            ->assertDontSee('Foo bar baz')
            ->waitForTextIn('@selected', 'Bar');
    }

    #[Test]
    public function can_render_and_select_with_accents(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-tab selected="Données d'identification">
                        <x-tab.items tab="Données d'identification">
                            Lorem ipsum dolor sit amet
                        </x-tab.items>
                        <x-tab.items tab="Données d'accès incorrectes">
                            Consectetur adipiscing elit
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Données d\'identification')
            ->assertSee('Lorem ipsum dolor sit amet')
            ->assertSee('Données d\'accès incorrectes')
            ->assertDontSee('Consectetur adipiscing elit')
            ->clickAtXPath('/html/body/div[3]/div/ul/li[2]')
            ->waitForText('Consectetur adipiscing elit')
            ->assertSee('Consectetur adipiscing elit')
            ->assertDontSee('Lorem ipsum dolor sit amet');
    }

    #[Test]
    public function can_render_left_slot_using_slot(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-tab selected="Foo">
                        <x-tab.items tab="Foo">
                            <x-slot:left>
                                TallStackUI                            
                            </x-slot:left>
                            Foo bar baz
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Foo bar baz')
            ->assertSee('TallStackUI');
    }

    #[Test]
    public function can_render_livewire_component(): void
    {
        Livewire::component('test', new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>
                    Foo bar baz through Livewire Component
                </div>
                HTML;
            }
        });

        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-tab selected="Foo">
                        <x-tab.items tab="Foo">
                            <livewire:test />
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Foo bar baz through Livewire Component');
    }

    #[Test]
    public function can_render_right_slot_using_slot(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-tab selected="Foo">
                        <x-tab.items tab="Foo">
                            <x-slot:right>
                                TallStackUI                            
                            </x-slot:right>
                            Foo bar baz
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Foo bar baz')
            ->assertSee('TallStackUI');
    }

    #[Test]
    public function can_render_slots_using_raw_strings(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-tab selected="Foo">
                        <x-tab.items tab="Foo" left="TallStackUI" right="Livewire">
                            Foo bar baz
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Foo bar baz')
            ->assertSee('TallStackUI')
            ->assertSee('Livewire');
    }

    #[Test]
    public function can_select(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-tab selected="Foo">
                        <x-tab.items tab="Foo">
                            Foo bar baz
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Foo bar baz')
            ->assertDontSee('Baz bar foo')
            ->clickAtXPath('/html/body/div[3]/div/ul/li[2]')
            ->waitForText('Baz bar foo')
            ->assertDontSee('Foo bar baz');
    }

    #[Test]
    public function can_select_and_deselect(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-tab selected="Foo">
                        <x-tab.items tab="Foo">
                            Foo bar baz
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Foo bar baz')
            ->assertDontSee('Baz bar foo')
            ->clickAtXPath('/html/body/div[3]/div/ul/li[2]')
            ->waitForText('Baz bar foo')
            ->assertDontSee('Foo bar baz')
            ->clickAtXPath('/html/body/div[3]/div/ul/li[1]')
            ->waitForText('Foo bar baz')
            ->assertDontSee('Baz bar foo');
    }

    #[Test]
    public function can_select_with_entangle(): void
    {
        Livewire::visit(new class extends Component
        {
            public string $tab = 'Bar';

            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    <x-tab wire:model="tab">
                        <x-tab.items tab="Foo">
                            Foo bar baz
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>

                    <x-button id="change" wire:click="$set('tab', 'Foo')" text="Click" />
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Baz bar foo')
            ->assertDontSee('Foo bar baz')
            ->click('#change')
            ->waitForText('Foo bar baz')
            ->assertDontSee('Baz bar foo');
    }

    #[Test]
    public function can_select_with_entangle_live(): void
    {
        Livewire::visit(new class extends Component
        {
            public string $tab = 'Bar';

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    {{ $tab }}

                    <x-tab wire:model.live="tab">
                        <x-tab.items tab="Foo">
                            Foo bar baz
                        </x-tab.items>
                        <x-tab.items tab="Bar">
                            Baz bar foo
                        </x-tab.items>
                    </x-tab>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Baz bar foo')
            ->assertDontSee('Foo bar baz')
            ->clickAtXPath('/html/body/div[3]/div/ul/li[1]')
            ->waitForText('Foo bar baz')
            ->assertSee('Bar')
            ->assertDontSee('Baz bar foo');
    }
}
