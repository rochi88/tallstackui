<?php

namespace TallStackUi\View\Components\Form;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use TallStackUi\Foundation\Attributes\PassThroughRuntime;
use TallStackUi\Foundation\Attributes\SkipDebug;
use TallStackUi\Foundation\Attributes\SoftPersonalization;
use TallStackUi\Foundation\Personalization\Contracts\Personalization;
use TallStackUi\Foundation\Support\Runtime\Components\ColorRuntime;
use TallStackUi\TallStackUiComponent;
use TallStackUi\View\Components\Floating;

#[SoftPersonalization('form.color')]
#[PassThroughRuntime(ColorRuntime::class)]
class Color extends TallStackUiComponent implements Personalization
{
    public function __construct(
        public ?string $label = null,
        public ?string $hint = null,
        public ?bool $picker = false,
        public Collection|array|null $colors = null,
        public ?bool $invalidate = null,
        public ?bool $selectable = null,
        #[SkipDebug]
        public ?string $mode = null,
    ) {
        $this->mode = $this->picker ? 'picker' : 'range';
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.form.color');
    }

    public function personalization(): array
    {
        return Arr::dot([
            'selected' => [
                'wrapper' => 'flex items-center',
                'base' => 'dark:border-dark-700 h-6 w-6 rounded shadow',
            ],
            'icon' => [
                'class' => 'h-5 w-5',
            ],
            'floating' => [
                'default' => collect(app(Floating::class)->personalization())->get('wrapper'),
                'class' => 'w-[18rem] overflow-auto',
            ],
            'box' => [
                'base' => 'shadow-xs dark:bg-dark-700 soft-scrollbar max-h-60 overflow-auto rounded-md bg-white py-4',
                'range' => [
                    'wrapper' => 'px-4',
                    'base' => 'mb-4 h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200 dark:bg-gray-600',
                    'thumb' => '[&::-webkit-slider-thumb]:bg-primary-500 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full',
                ],
                'button' => [
                    'wrapper' => 'mx-auto flex w-[17rem] flex-wrap items-center justify-center gap-1',
                    'base' => 'rounded shadow-lg',
                    'color' => 'flex h-5 w-5 cursor-pointer items-center justify-center rounded',
                    'icon' => 'h-3 w-3',
                ],
            ],
            'clearable' => [
                'wrapper' => 'cursor-pointer flex items-center text-gray-500 dark:text-dark-400',
                'padding' => 'pr-1.5',
                'size' => 'h-5 w-5',
                'color' => 'text-gray-500 dark:text-dark-400',
            ],
        ]);
    }

    /** @throws InvalidArgumentException */
    protected function validate(): void
    {
        $colors = collect($this->colors);

        if ($colors->isEmpty()) {
            return;
        }

        $colors->each(function (string $color) {
            if (! str($color)->startsWith('#')) {
                throw new InvalidArgumentException('All the [colors] must starts with #');
            }
        });
    }
}
