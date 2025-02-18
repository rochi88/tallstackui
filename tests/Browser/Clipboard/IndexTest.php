<?php

namespace Tests\Browser\Clipboard;

use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\OperatingSystem;
use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\BrowserTestCase;

class IndexTest extends BrowserTestCase
{
    #[Test]
    public function can_copy_when_icon(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $foo = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-clipboard text="e4da3b7fbbce2345d7772b0674a318d5" icon />
                    <input dusk="paste">
                </div>
            HTML;
            }
        })
            ->assertDontSee('Your API')
            ->click('@tallstackui_clipboard_icon_copy')
            ->keys('@paste', [OperatingSystem::onMac() ? WebDriverKeys::COMMAND : WebDriverKeys::CONTROL, 'v'])
            ->assertInputValue('@paste', 'e4da3b7fbbce2345d7772b0674a318d5');
    }

    #[Test]
    public function can_copy_when_input(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $foo = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-clipboard label="Your API" text="c4ca4238a0b923820dcc509a6f75849b" />
                    <input dusk="paste">
                </div>
            HTML;
            }
        })
            ->assertSee('Your API')
            ->click('@tallstackui_clipboard_input_copy')
            ->keys('@paste', [OperatingSystem::onMac() ? WebDriverKeys::COMMAND : WebDriverKeys::CONTROL, 'v'])
            ->assertInputValue('@paste', 'c4ca4238a0b923820dcc509a6f75849b');
    }

    #[Test]
    public function can_use_event(): void
    {
        Livewire::visit(new class extends Component
        {
            public ?string $copied = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="copied">{{ $copied }}</p>
                
                    <x-clipboard label="Your API" 
                                 text="c4ca4238a0b923820dcc509a6f75849b" 
                                 x-on:copy="$wire.set('copied', $event.detail.text)" />
                </div>
            HTML;
            }
        })
            ->assertSee('Your API')
            ->assertDontSeeIn('@copied', 'c4ca4238a0b923820dcc509a6f75849b')
            ->click('@tallstackui_clipboard_input_copy')
            ->waitForTextIn('@copied', 'c4ca4238a0b923820dcc509a6f75849b')
            ->assertSeeIn('@copied', 'c4ca4238a0b923820dcc509a6f75849b');
    }
}
