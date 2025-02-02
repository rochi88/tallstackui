<?php

namespace TallStackUi\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use TallStackUi\Foundation\Attributes\ColorsThroughOf;
use TallStackUi\Foundation\Attributes\SkipDebug;
use TallStackUi\Foundation\Attributes\SoftPersonalization;
use TallStackUi\Foundation\Personalization\Contracts\Personalization;
use TallStackUi\Foundation\Support\Colors\Components\BadgeColors;
use TallStackUi\TallStackUiComponent;

#[SoftPersonalization('badge')]
#[ColorsThroughOf(BadgeColors::class)]
class Badge extends TallStackUiComponent implements Personalization
{
    public function __construct(
        public ?string $text = null,
        public ?string $icon = null,
        public ?string $position = 'right',
        public ?bool $xs = null,
        public ?bool $sm = null,
        public ?bool $md = null,
        public ?bool $lg = null,
        public ?string $color = 'primary',
        public ?bool $square = false,
        public ?bool $round = false,
        public ?bool $solid = true,
        public ?bool $outline = null,
        public ?bool $light = null,
        #[SkipDebug]
        public ?string $size = null,
        #[SkipDebug]
        public ?string $style = null,
        #[SkipDebug]
        public ?string $left = null,
        #[SkipDebug]
        public ?string $right = null,
    ) {
        $this->style = $this->outline ? 'outline' : ($this->light ? 'light' : 'solid');
        $this->size = $this->lg ? 'lg' : ($this->md ? 'md' : ($this->sm ? 'sm' : 'xs'));
        $this->position = $this->position === 'right' ? 'right' : 'left';
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.badge');
    }

    public function personalization(): array
    {
        return Arr::dot([
            'wrapper' => [
                'class' => 'outline-hidden inline-flex items-center border px-2 py-0.5 font-bold',
                'sizes' => [
                    'xs' => 'text-xs',
                    'sm' => 'text-sm',
                    'md' => 'text-md',
                    'lg' => 'text-lg',
                ],
            ],
            'clickable' => 'cursor-pointer',
            'icon' => 'h-3 w-3',
            'border.radius' => [
                'rounded' => 'rounded-md',
                'circle' => 'rounded-full',
            ],
        ]);
    }
}
