<?php

namespace TallStackUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use TallStackUi\Foundation\Attributes\ColorsThroughOf;
use TallStackUi\Foundation\Attributes\SoftPersonalization;
use TallStackUi\Foundation\Personalization\Contracts\Personalization;
use TallStackUi\Foundation\Support\Colors\Components\BooleanColors;
use TallStackUi\TallStackUiComponent;

#[SoftPersonalization('boolean')]
#[ColorsThroughOf(BooleanColors::class)]
class Boolean extends TallStackUiComponent implements Personalization
{
    public function __construct(
        public bool|Closure $boolean = false,
        public ?string $iconWhenTrue = null,
        public ?string $iconWhenFalse = null,
        public ?string $colorWhenTrue = null,
        public ?string $colorWhenFalse = null,
    ) {
        $this->boolean = value($this->boolean);

        $this->iconWhenTrue ??= 'check-circle';
        $this->iconWhenFalse ??= 'check-circle';
        $this->colorWhenTrue ??= 'primary';
        $this->colorWhenFalse ??= 'gray';
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.boolean');
    }

    public function personalization(): array
    {
        return ['icon' => 'w-5 h-5'];
    }
}
