<?php

namespace TallStackUi\View\Components\Step;

use Illuminate\Contracts\View\View;
use TallStackUi\TallStackUiComponent;

class Items extends TallStackUiComponent
{
    public function __construct(
        public int $step,
        public ?string $title = null,
        public ?string $description = null,
        public ?bool $completed = false
    ) {
        //
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.step.items');
    }
}
