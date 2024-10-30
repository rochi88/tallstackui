<?php

namespace TallStackUi\View\Components\Select;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use TallStackUi\Foundation\Attributes\PassThroughRuntime;
use TallStackUi\Foundation\Attributes\SoftPersonalization;
use TallStackUi\Foundation\Personalization\Contracts\Personalization;
use TallStackUi\Foundation\Support\Runtime\Components\SelectNativeRuntime;
use TallStackUi\TallStackUiComponent;
use TallStackUi\View\Components\Form\Traits\DefaultInputClasses;
use TallStackUi\View\Components\Select\Traits\Setup;

#[SoftPersonalization('select.native')]
#[PassThroughRuntime(SelectNativeRuntime::class)]
class Native extends TallStackUiComponent implements Personalization
{
    use DefaultInputClasses;
    use Setup;

    public function __construct(
        public ?string $label = null,
        public ?string $hint = null,
        public Collection|array $options = [],
        public ?string $select = null,
        public ?array $selectable = [],
        public ?bool $invalidate = null,
        public ?bool $grouped = null,
    ) {
        //
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.select.native');
    }

    public function personalization(): array
    {
        return Arr::dot([
            'wrapper' => 'relative mt-1',
            'input' => [...$this->input()],
            'error' => $this->error('focus:ring-2'),
        ]);
    }

    /** @throws InvalidArgumentException */
    protected function validate(): void
    {
        if (($this->options !== [] && is_array($this->options[0])) && ! $this->select) {
            throw new InvalidArgumentException('The [select] parameter must be defined');
        }
    }
}
