<?php

namespace TallStackUi\View\Components\Layout\SideBar;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentSlot;
use TallStackUi\Foundation\Attributes\SoftPersonalization;
use TallStackUi\Foundation\Personalization\Contracts\Personalization;
use TallStackUi\TallStackUiComponent;

#[SoftPersonalization('layout.side-bar')]
class SideBar extends TallStackUiComponent implements Personalization
{
    public function __construct(public ?ComponentSlot $brand = null)
    {
        //
    }

    public function blade(): View
    {
        return view('tallstack-ui::components.layout.sidebar.sidebar');
    }

    public function personalization(): array
    {
        return Arr::dot([
            'mobile' => [
                'wrapper' => [
                    'first' => 'relative z-50 md:hidden',
                    'second' => 'fixed inset-0 flex',
                    'third' => 'relative mr-16 flex w-full max-w-xs flex-1',
                    'fourth' => 'flex grow flex-col gap-y-5 overflow-y-auto bg-white px-2 pb-4',
                    'fifth' => 'mt-10 flex h-16 shrink-0 items-center',
                    'sixth' => 'flex h-16 flex-1 flex-col',
                    'seventh' => 'flex flex-1 flex-col gap-y-2',
                ],
                'button' => [
                    'wrapper' => 'absolute left-full top-0 flex w-16 justify-center pt-5',
                    'size' => 'w-6 h-6 text-white',
                    'icon' => 'x-mark',
                ],
            ],
            'desktop' => [
                'wrapper' => [
                    'first' => 'hidden md:fixed md:inset-y-0 md:z-50 md:flex md:w-72 md:flex-col',
                    'second' => 'flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-2 pb-4',
                    'third' => 'mt-10 flex h-16 shrink-0 items-center',
                    'fourth' => 'flex h-16 flex-1 flex-col',
                    'fifth' => 'flex flex-1 flex-col',
                ],
            ],
        ]);
    }
}
