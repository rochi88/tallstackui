<?php

namespace Tests\Browser\Table;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
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
            public array $rows = [
                ['id' => 1, 'name' => 'Foo'],
                ['id' => 2, 'name' => 'Bar'],
            ];

            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    @php
                        $headers = [
                            ['index' => 'id', 'label' => '#'],
                            ['index' => 'name', 'label' => 'Name'],
                        ];
                    @endphp
                    <x-table :$headers :$rows filter loading />
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertSee('Quantity');
    }

    #[Test]
    public function can_render_headless(): void
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    @php
                        $rows = [
                            ['id' => 1, 'name' => 'Foo'],
                            ['id' => 2, 'name' => 'Bar'],
                        ];
                
                        $headers = [
                            ['index' => 'id', 'label' => '#'],
                            ['index' => 'name', 'label' => 'Name'],
                        ];
                    @endphp
                    
                    <x-table :$headers :$rows headerless />
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertDontSee('Name');
    }

    #[Test]
    public function can_render_manipulating_columns()
    {
        Livewire::visit(new class extends Component
        {
            public array $rows = [
                ['id' => 1, 'name' => 'Foo'],
                ['id' => 2, 'name' => 'Bar'],
            ];

            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    @php
                        $headers = [
                            ['index' => 'id', 'label' => '#'],
                            ['index' => 'name', 'label' => 'Name'],
                        ];
                    @endphp
                    <x-table :$headers :$rows filter loading>
                        @interact('column_name', $row)
                            {{ $row['name'] }} Test
                        @endinteract
                    </x-table>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo Test')
            ->assertSee('Bar Test');
    }

    #[Test]
    public function can_render_manipulating_columns_passing_extra_variables()
    {
        Livewire::visit(new class extends Component
        {
            public array $rows = [
                ['id' => 1, 'name' => 'Foo'],
                ['id' => 2, 'name' => 'Bar'],
            ];

            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    @php
                        $headers = [
                            ['index' => 'id', 'label' => '#'],
                            ['index' => 'name', 'label' => 'Name'],
                        ];
                        
                        $extra = 'Extra';
                    @endphp
                    <x-table :$headers :$rows filter loading>
                        @interact('column_name', $row, $extra)
                            {{ $row['name'] }} Test {{ $extra }}
                        @endinteract
                    </x-table>
                </div>
                HTML;
            }
        })
            ->assertSee('Foo Test Extra')
            ->assertSee('Bar Test Extra');
    }

    #[Test]
    public function can_render_manipulating_columns_without_parameters()
    {
        Livewire::visit(new class extends Component
        {
            public array $rows = [
                ['id' => 1, 'name' => 'Foo'],
                ['id' => 2, 'name' => 'Bar'],
            ];

            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    @php
                        $headers = [
                            ['index' => 'id', 'label' => '#'],
                            ['index' => 'name', 'label' => 'Name'],
                        ];
                    @endphp
                    <x-table :$headers :$rows filter loading>
                        @interact('column_name')
                            Test
                        @endinteract
                    </x-table>
                </div>
                HTML;
            }
        })
            ->assertDontSee('Foo')
            ->assertDontSee('Bar')
            ->assertSee('Test');
    }

    #[Test]
    public function can_render_paginated(): void
    {
        Livewire::visit(new class extends Component
        {
            #[Computed]
            public function rows(): LengthAwarePaginator
            {
                $items = collect([
                    ['id' => 1, 'name' => 'Foo'],
                    ['id' => 2, 'name' => 'Bar'],
                    ['id' => 3, 'name' => 'Baz'],
                    ['id' => 4, 'name' => 'Qux'],
                    ['id' => 5, 'name' => 'Quux'],
                    ['id' => 6, 'name' => 'Quuz'],
                    ['id' => 7, 'name' => 'Corge'],
                    ['id' => 8, 'name' => 'Grault'],
                    ['id' => 9, 'name' => 'Garply'],
                    ['id' => 10, 'name' => 'Waldo'],
                    ['id' => 11, 'name' => 'Fred'],
                    ['id' => 12, 'name' => 'Plugh'],
                    ['id' => 13, 'name' => 'Xyzzy'],
                    ['id' => 14, 'name' => 'Thud'],
                ]);

                return new LengthAwarePaginator($items->forPage(1, 5), $items->count(), 5, 1);
            }

            public function render(): string
            {
                return <<<'HTML'
                <div>        
                    @php
                        $headers = [
                            ['index' => 'id', 'label' => '#'],
                            ['index' => 'name', 'label' => 'Name'],
                        ];
                    @endphp
                    <x-table :$headers :rows="$this->rows" paginate id="foo" />
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->assertDontSee('Quantity')
            ->assertSee('Showing 1 to 5 of 14 results');
    }

    #[Test]
    public function can_render_selectable_and_select_rows(): void
    {
        Livewire::visit(new class extends Component
        {
            public array $rows = [
                ['id' => 1, 'name' => 'Foo'],
                ['id' => 2, 'name' => 'Bar'],
            ];

            public array $selected = [];

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ implode(',', $selected) }}</p>

                    @php
                        $headers = [
                            ['index' => 'id', 'label' => '#'],
                            ['index' => 'name', 'label' => 'Name'],
                        ];
                    @endphp
                    
                    <x-table wire:model.live="selected" :$headers :$rows selectable />
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->click('@tallstackui_table_select_all')
            ->waitForTextIn('@selected', '1,2');

    }

    #[Test]
    public function can_render_selectable_and_select_rows_using_different_property(): void
    {
        Livewire::visit(new class extends Component
        {
            public array $rows = [
                ['id' => 1, 'name' => 'Foo', 'email' => 'foo@bar.com'],
                ['id' => 2, 'name' => 'Bar', 'email' => 'bar@foo.com'],
            ];

            public array $selected = [];

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="selected">{{ implode(',', $selected) }}</p>

                    @php
                        $headers = [
                            ['index' => 'id', 'label' => '#'],
                            ['index' => 'name', 'label' => 'Name'],
                        ];
                    @endphp
                    
                    <x-table wire:model.live="selected" :$headers :$rows selectable selectable-property="email" />
                </div>
                HTML;
            }
        })
            ->assertSee('Foo')
            ->assertSee('Bar')
            ->click('@tallstackui_table_select_all')
            ->waitForTextIn('@selected', 'foo@bar.com,bar@foo.com');
    }
}
