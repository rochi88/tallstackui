<?php

namespace Tests\Browser\Interactions\Dialog;

use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use TallStackUi\Traits\Interactions;
use Tests\Browser\BrowserTestCase;

class IndexTest extends BrowserTestCase
{
    #[Test]
    public function can_dispatch_confirmation_dialog_without_livewire_specifing_component_id()
    {
        Livewire::visit(new class extends Component
        {
            use Interactions;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-button dusk="confirm" onclick="confirm()">Confirm</x-button>
                    
                    <script>
                        confirm = () => $interaction('dialog').question('Confirm?')
                            .wireable(Livewire.first().id)
                            .confirm('Confirm', 'confirmed', 'Confirmed Without Livewire')
                            .cancel('Cancel', 'cancelled', 'Cancelled Without Livewire')
                            .send();
                    </script>
                </div>
                HTML;
            }

            public function confirmed(string $message): void
            {
                $this->dialog()->success($message)->send();
            }

            public function cancelled(string $message): void
            {
                $this->dialog()->error($message)->send();
            }
        })
            ->assertDontSee('Confirm?')
            ->assertDontSee('Confirmed Without Livewire')
            ->click('@confirm')
            ->waitForText('Confirm?')
            ->click('@tallstackui_dialog_confirmation')
            ->waitForText('Confirmed Without Livewire')
            ->assertSee('Confirmed Without Livewire')
            ->click('@tallstackui_dialog_confirmation')
            ->click('@confirm')
            ->waitForText('Confirm?')
            ->click('@tallstackui_dialog_rejection')
            ->waitForText('Cancelled Without Livewire')
            ->assertSee('Cancelled Without Livewire');
    }

    #[Test]
    public function can_dispatch_confirmation_dialog_without_livewire_using_first_component_in_page()
    {
        Livewire::visit(new class extends Component
        {
            use Interactions;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-button dusk="confirm" onclick="confirm()">Confirm</x-button>
                    
                    <script>
                        confirm = () => $interaction('dialog').question('Confirm?')
                            .wireable()
                            .confirm('Confirm', 'confirmed', 'Confirmed Without Livewire')
                            .cancel('Cancel', 'cancelled', 'Cancelled Without Livewire')
                            .send();
                    </script>
                </div>
                HTML;
            }

            public function confirmed(string $message): void
            {
                $this->dialog()->success($message)->send();
            }
        })
            ->assertDontSee('Confirm?')
            ->assertDontSee('Confirmed Without Livewire')
            ->click('@confirm')
            ->waitForText('Confirm?')
            ->click('@tallstackui_dialog_confirmation')
            ->waitForText('Confirmed Without Livewire')
            ->assertSee('Confirmed Without Livewire');
    }

    #[Test]
    public function can_dispatch_dialog_without_livewire()
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-button dusk="success" x-on:click="$interaction('dialog').success('Success Without Livewire').send()">Success</x-button>
                    <x-button dusk="error" x-on:click="$interaction('dialog').error('Error Without Livewire').send()">Error</x-button>
                    <x-button dusk="info" x-on:click="$interaction('dialog').info('Info Without Livewire').send()">Info</x-button>
                    <x-button dusk="warning" x-on:click="$interaction('dialog').warning('Warning Without Livewire').send()">Error</x-button>
                </div>
                HTML;
            }
        })
            ->assertDontSee('Success Without Livewire')
            ->click('@success')
            ->waitForText('Success Without Livewire')
            ->click('@tallstackui_dialog_confirmation')
            ->assertDontSee('Error Without Livewire')
            ->click('@error')
            ->waitForText('Error Without Livewire')
            ->click('@tallstackui_dialog_confirmation')
            ->assertDontSee('Info Without Livewire')
            ->click('@info')
            ->waitForText('Info Without Livewire')
            ->click('@tallstackui_dialog_confirmation')
            ->assertDontSee('Warning Without Livewire')
            ->click('@warning')
            ->waitForText('Warning Without Livewire')
            ->click('@tallstackui_dialog_confirmation');
    }

    #[Test]
    public function can_dispatch_dismissed_event()
    {
        $this->skipOnGitHubActions();

        Livewire::visit(new class extends Component
        {
            use Interactions;

            public ?string $target = null;

            public function success(): void
            {
                $this->dialog()->success('Foo bar success', 'Foo bar success description')->send();
            }

            public function render(): string
            {
                return <<<'HTML'
                <div x-on:dialog:dismissed.window="$wire.set('target', 'Dismissed')">
                    @if ($target)
                        <p dusk="target">{{ $target }}</p>
                    @endif
                
                    <x-button dusk="success" wire:click="success">Success</x-button>
                </div>
                HTML;
            }
        })
            ->assertNotPresent('@target')
            ->assertSee('Success')
            ->click('@success')
            ->waitForText(['Foo bar success', 'Foo bar success description'])
            ->assertSee('Foo bar success')
            ->assertSee('Foo bar success description')
            ->clickAtPoint(350, 350)
            ->pause(100)
            ->assertPresent('@target');
    }

    #[Test]
    public function can_dispatch_events()
    {
        Livewire::visit(new class extends Component
        {
            use Interactions;

            public string $target = '';

            public function cancelled(string $message): void
            {
                $this->dialog()->success($message)->send();
            }

            public function confirm(): void
            {
                $this->dialog()
                    ->question('Foo bar confirmation', 'Foo bar confirmation description')
                    ->confirm('Confirm', 'confirmed', 'Foo bar confirmed foo')
                    ->cancel('Cancel', 'cancelled', 'Bar foo cancelled bar')
                    ->send();
            }

            public function confirmed(string $message): void
            {
                $this->dialog()->success($message)->send();
            }

            public function render(): string
            {
                return <<<'HTML'
                <div x-on:dialog:accepted.window="$wire.set('target', 'Accepted')" 
                     x-on:dialog:rejected.window="$wire.set('target', 'Rejected')">
                    <p dusk="target">{{ $target }}</p>
                
                    <x-button dusk="confirm" wire:click="confirm">Confirm</x-button>
                </div>
                HTML;
            }
        })
            ->assertDontSee('Accepted')
            ->assertDontSee('Rejected')
            ->assertSee('Confirm')
            ->click('@confirm')
            ->waitForText('Foo bar confirmation')
            ->assertSee('Foo bar confirmation')
            ->click('@tallstackui_dialog_confirmation')
            ->waitForText('Accepted')
            ->assertSeeIn('@target', 'Accepted')
            ->waitForText('Foo bar confirmed foo')
            ->assertSee('Foo bar confirmed foo')
            ->click('@tallstackui_dialog_confirmation')
            ->click('@confirm')
            ->waitForText('Foo bar confirmation')
            ->assertSee('Foo bar confirmation')
            ->click('@tallstackui_dialog_rejection')
            ->waitForText('Rejected')
            ->assertSee('Rejected')
            ->waitForText('Bar foo cancelled bar')
            ->assertSee('Bar foo cancelled bar');
    }

    #[Test]
    public function can_send(): void
    {
        Livewire::visit(DialogComponent::class)
            ->assertDontSee('Foo bar success')
            ->click('@success')
            ->waitForText('Foo bar success')
            ->click('@tallstackui_dialog_confirmation')
            ->assertDontSee('Foo bar error')
            ->click('@error')
            ->waitForText('Foo bar error')
            ->click('@tallstackui_dialog_confirmation')
            ->assertDontSee('Foo bar info')
            ->click('@info')
            ->waitForText('Foo bar info')
            ->click('@tallstackui_dialog_confirmation')
            ->assertDontSee('Foo bar warning')
            ->click('@warning')
            ->waitForText('Foo bar warning')
            ->click('@tallstackui_dialog_confirmation');
    }

    #[Test]
    public function can_send_cancellation(): void
    {
        Livewire::visit(DialogComponent::class)
            ->assertDontSee('Foo bar confirmation description')
            ->click('@confirm')
            ->waitForText('Foo bar confirmation description')
            ->click('@tallstackui_dialog_rejection')
            ->waitForText('Bar foo cancelled bar');
    }

    #[Test]
    public function can_send_confirmation(): void
    {
        Livewire::visit(DialogComponent::class)
            ->assertDontSee('Foo bar confirmation description')
            ->click('@confirm')
            ->waitForText('Foo bar confirmation description')
            ->click('@tallstackui_dialog_confirmation')
            ->waitUntilMissingText('Foo bar confirmation description');
    }

    #[Test]
    public function can_use_close_hook()
    {
        Livewire::visit(new class extends Component
        {
            use Interactions;

            public ?string $close = null;

            public function success(): void
            {
                $this->dialog()
                    ->success('Foo!')
                    ->hook([
                        'close' => [
                            'method' => 'hook',
                            'params' => 'close',
                        ],
                    ])
                    ->send();
            }

            public function hook(string $term): void
            {
                $this->close = $term;
            }

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="close">{{ $close }}</p>
                
                    <x-button dusk="success" wire:click="success">Success</x-button>
                </div>
                HTML;
            }
        })
            ->assertDontSee('close')
            ->assertDontSee('Foo')
            ->assertSee('Success')
            ->click('@success')
            ->waitForText('Foo')
            ->assertSee('Foo')
            ->click('@tallstackui_dialog_close')
            ->waitForTextIn('@close', 'close')
            ->assertSee('close');
    }

    #[Test]
    public function can_use_dismiss_hook()
    {
        Livewire::visit(new class extends Component
        {
            use Interactions;

            public ?string $dismiss = null;

            public function success(): void
            {
                $this->dialog()
                    ->success('Foo!')
                    ->hook([
                        'dismiss' => [
                            'method' => 'hook',
                            'params' => 'dismiss',
                        ],
                    ])
                    ->send();
            }

            public function hook(string $term): void
            {
                $this->dismiss = $term;
            }

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="dismiss">{{ $dismiss }}</p>
                
                    <x-button dusk="success" wire:click="success">Success</x-button>
                </div>
                HTML;
            }
        })
            ->assertDontSee('dismiss')
            ->assertDontSee('Foo')
            ->assertSee('Success')
            ->click('@success')
            ->waitForText('Foo')
            ->assertSee('Foo')
            ->clickAtPoint(350, 350)
            ->waitForTextIn('@dismiss', 'dismiss')
            ->assertSee('dismiss');
    }

    #[Test]
    public function can_use_ok_hook()
    {
        Livewire::visit(new class extends Component
        {
            use Interactions;

            public ?string $ok = null;

            public function success(): void
            {
                $this->dialog()
                    ->success('Foo!')
                    ->hook([
                        'ok' => [
                            'method' => 'hook',
                            'params' => 'ok',
                        ],
                    ])
                    ->send();
            }

            public function hook(string $term): void
            {
                $this->ok = $term;
            }

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <p dusk="ok">{{ $ok }}</p>
                
                    <x-button dusk="success" wire:click="success">Success</x-button>
                </div>
                HTML;
            }
        })
            ->assertDontSee('ok')
            ->assertDontSee('Foo')
            ->assertSee('Success')
            ->click('@success')
            ->waitForText('Foo')
            ->assertSee('Foo')
            ->click('@tallstackui_dialog_confirmation')
            ->waitForTextIn('@ok', 'ok')
            ->assertSee('ok');
    }

    #[Test]
    public function cannot_close_when_dialog_is_persistent(): void
    {
        config()->set('tallstackui.settings.dialog.persistent', true);

        Livewire::visit(DialogComponent::class)
            ->assertDontSee('Foo bar success')
            ->click('@success')
            ->waitForText('Foo bar success')
            ->assertSee('Foo bar success')
            ->clickAtPoint(350, 350)
            ->pause(100)
            ->waitForText('Foo bar success')
            ->assertSee('Foo bar success');
    }

    #[Test]
    public function cannot_see_cancellation_if_it_was_not_defined(): void
    {
        Livewire::visit(new class extends Component
        {
            use Interactions;

            public function confirm(): void
            {
                $this->dialog()
                    ->question('Foo bar confirmation', 'Foo bar confirmation description')
                    ->confirm('Confirm', 'confirmed', 'Foo bar confirmed foo')
                    ->send();
            }

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-button dusk="confirm" wire:click="confirm">Confirm</x-button>
                </div>
                HTML;
            }
        })
            ->assertDontSee('Foo bar confirmation')
            ->click('@confirm')
            ->waitForText('Foo bar confirmation')
            ->assertSee('Foo bar confirmation')
            ->assertSee('Foo bar confirmation description')
            ->assertDontSee('Cancelled');
    }
}

class DialogComponent extends Component
{
    use Interactions;

    public function cancelled(string $message): void
    {
        $this->dialog()->success($message)->send();
    }

    public function confirm(): void
    {
        $this->dialog()
            ->question('Foo bar confirmation', 'Foo bar confirmation description')
            ->confirm('Confirm', 'confirmed', 'Foo bar confirmed foo')
            ->cancel('Cancel', 'cancelled', 'Bar foo cancelled bar')
            ->send();
    }

    public function confirmed(string $message): void
    {
        $this->dialog()->success($message)->send();
    }

    public function error(): void
    {
        $this->dialog()->error('Foo bar error')->send();
    }

    public function info(): void
    {
        $this->dialog()->info('Foo bar info')->send();
    }

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button dusk="success" wire:click="success">Success</x-button>
            <x-button dusk="error" wire:click="error">Error</x-button>
            <x-button dusk="info" wire:click="info">Info</x-button>
            <x-button dusk="warning" wire:click="warning">Error</x-button>
            <x-button dusk="confirm" wire:click="confirm">Confirm</x-button>
        </div>
        HTML;
    }

    public function success(): void
    {
        $this->dialog()->success('Foo bar success')->send();
    }

    public function warning(): void
    {
        $this->dialog()->warning('Foo bar warning')->send();
    }
}
