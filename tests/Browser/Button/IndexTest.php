<?php

namespace Tests\Browser\Button;

use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;
use Tests\Browser\Button\Components\ButtonComponent;
use Tests\Browser\Button\Components\CircleComponent;

class IndexTest extends BrowserTestCase
{
    /** @test */
    public function can_see_loading_spinner(): void
    {
        $this->browse(function (Browser $browser) {
            $this->visit($browser, ButtonComponent::class)
                ->assertDontSee('svg')
                ->type('input', 'Foo bar')
                ->click('#delay')
                ->waitFor('@button-loading-spinner');
        });

        $this->browse(function (Browser $browser) {
            $this->visit($browser, CircleComponent::class)
                ->assertDontSee('svg')
                ->type('input', 'Foo bar')
                ->click('#delay')
                ->waitFor('@button-loading-spinner');
        });
    }
}
