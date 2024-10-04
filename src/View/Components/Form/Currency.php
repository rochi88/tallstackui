<?php

namespace TallStackUi\View\Components\Form;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use TallStackUi\Foundation\Attributes\PassThroughRuntime;
use TallStackUi\Foundation\Attributes\SkipDebug;
use TallStackUi\Foundation\Attributes\SoftPersonalization;
use TallStackUi\Foundation\Personalization\Contracts\Personalization;
use TallStackUi\Foundation\Support\Runtime\Components\InputRuntime;
use TallStackUi\TallStackUiComponent;
use TallStackUi\View\Components\Form\Traits\DefaultInputClasses;

#[SoftPersonalization('form.currency')]
#[PassThroughRuntime(InputRuntime::class)]
class Currency extends TallStackUiComponent implements Personalization
{
    use DefaultInputClasses;

    public function __construct(
        public ?string $label = null,
        public ?string $hint = null,
        public ?string $icon = null,
        public ?bool $clearable = null,
        public ?bool $invalidate = null,
        public ?string $locale = 'en-US',
        #[SkipDebug]
        public ?string $prefix = null,
        #[SkipDebug]
        public ?string $suffix = null,
        #[SkipDebug]
        public ?string $position = 'left',
    ) {
        $this->position = $this->position === 'left' ? 'left' : 'right';
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.form.currency');
    }

    public function personalization(): array
    {
        return Arr::dot([
            'input' => [
                ...$this->input(),
                'paddings' => [
                    'prefix' => 'pr-3 pl-0',
                    'suffix' => 'pl-3 pr-0',
                    'left' => 'pl-8',
                    'right' => 'pr-8',
                    'clearable' => '!pr-14',
                ],
            ],
            'icon' => [
                'wrapper' => 'pointer-events-none absolute inset-y-0 flex items-center text-gray-500 dark:text-dark-400',
                'paddings' => [
                    'left' => 'left-0 pl-2',
                    'right' => 'right-0 pr-2',
                ],
                'size' => 'h-5 w-5',
                'color' => 'text-gray-500 dark:text-dark-400',
            ],
            'clearable' => [
                'wrapper' => 'cursor-pointer absolute inset-y-0 flex items-center text-gray-500 dark:text-dark-400',
                'padding' => 'right-0 pr-2',
                'size' => 'h-5 w-5',
                'color' => 'text-gray-500 dark:text-dark-400',
            ],
            'error' => $this->error(),
        ]);
    }
}
