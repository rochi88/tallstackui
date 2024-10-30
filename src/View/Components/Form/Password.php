<?php

namespace TallStackUi\View\Components\Form;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use TallStackUi\Foundation\Attributes\PassThroughRuntime;
use TallStackUi\Foundation\Attributes\SoftPersonalization;
use TallStackUi\Foundation\Personalization\Contracts\Personalization;
use TallStackUi\Foundation\Support\Runtime\Components\PasswordRuntime;
use TallStackUi\TallStackUiComponent;
use TallStackUi\View\Components\Floating;

#[SoftPersonalization('form.password')]
#[PassThroughRuntime(PasswordRuntime::class)]
class Password extends TallStackUiComponent implements Personalization
{
    public function __construct(
        public ?string $label = null,
        public ?string $hint = null,
        public Collection|array|null $rules = null,
        public ?bool $mixedCase = false,
        public ?bool $generator = false,
        public ?bool $invalidate = null
    ) {
        $this->rules = collect($this->rules);
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.form.password');
    }

    public function personalization(): array
    {
        return Arr::dot([
            'icon' => [
                'wrapper' => 'flex items-center',
                'class' => 'h-5 w-5 cursor-pointer',
            ],
            'floating' => collect(app(Floating::class)->personalization())->get('wrapper'),
            'rules' => [
                'title' => 'text-md font-semibold text-red-500 dark:text-dark-300',
                'block' => 'mt-2 flex flex-col',
                'items' => [
                    'base' => 'inline-flex items-center gap-1 text-gray-700 text-sm dark:text-dark-300',
                    'icons' => [
                        'error' => 'h-5 w-5 text-red-500',
                        'success' => 'h-5 w-5 text-green-500',
                    ],
                ],
            ],
        ]);
    }

    protected function setup(): void
    {
        $this->rules = collect($this->rules)->reduce(function (Collection $carry, string $value) {
            $defaults = __ts_configuration('settings.form.password.rules');

            if (str_contains($value, 'min')) {
                $carry->put('min', (explode(':', $value)[1] ?? $defaults['min']));
            }

            if (str_contains($value, 'numbers')) {
                $carry->put('numbers', true);
            }

            if (str_contains($value, 'symbols')) {
                $carry->put('symbols', (explode(':', $value)[1] ?? $defaults['symbols']));
            }

            if (str_contains($value, 'mixed')) {
                $carry->put('mixed', true);
            }

            return $carry;
        }, collect());
    }

    /** @throws Exception */
    protected function validate(): void
    {
        if ($this->generator && (! $this->rules || $this->rules->isEmpty())) {
            throw new Exception('The password [generator] requires the [rules] of the password.');
        }
    }
}
