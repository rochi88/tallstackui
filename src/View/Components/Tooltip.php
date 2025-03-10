<?php

namespace TallStackUi\View\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use TallStackUi\Foundation\Attributes\ColorsThroughOf;
use TallStackUi\Foundation\Attributes\PassThroughRuntime;
use TallStackUi\Foundation\Attributes\SkipDebug;
use TallStackUi\Foundation\Attributes\SoftPersonalization;
use TallStackUi\Foundation\Exceptions\InvalidSelectedPositionException;
use TallStackUi\Foundation\Personalization\Contracts\Personalization;
use TallStackUi\Foundation\Support\Colors\Components\TooltipColors;
use TallStackUi\Foundation\Support\Concerns\BuildRawIcon;
use TallStackUi\Foundation\Support\Icons\IconGuideMap;
use TallStackUi\Foundation\Support\Runtime\Components\TooltipRuntime;
use TallStackUi\TallStackUiComponent;

#[SoftPersonalization('tooltip')]
#[ColorsThroughOf(TooltipColors::class)]
#[PassThroughRuntime(TooltipRuntime::class)]
class Tooltip extends TallStackUiComponent implements Personalization
{
    use BuildRawIcon;

    /** @throws Exception */
    public function __construct(
        public ?string $text = null,
        public ?string $icon = 'question-mark-circle',
        public string $color = 'primary',
        public ?bool $xs = null,
        public ?bool $sm = null,
        public ?bool $md = null,
        public ?bool $lg = null,
        public ?string $position = 'top',
        #[SkipDebug]
        public ?string $size = null,
    ) {
        $this->icon = IconGuideMap::internal($this->icon);
        $this->size = $this->lg ? 'lg' : ($this->md ? 'md' : ($this->xs ? 'xs' : 'sm'));
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.tooltip');
    }

    public function personalization(): array
    {
        return Arr::dot([
            'wrapper' => 'inline-flex',
            'sizes' => [
                'xs' => 'h-4 w-4',
                'sm' => 'h-5 w-5',
                'md' => 'h-6 w-6',
                'lg' => 'h-7 w-7',
            ],
        ]);
    }

    /** @throws InvalidSelectedPositionException */
    protected function validate(): void
    {
        InvalidSelectedPositionException::validate(static::class, $this->position);
    }
}
