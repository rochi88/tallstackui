@php
    $personalize = $classes();
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @if ($navigate) wire:navigate @elseif($navigateHover) wire:navigate.hover @endif @endif
     {{ $attributes->class([
        $personalize['wrapper.first'],
        'cursor-pointer' => $tag === 'a',
     ]) }}
     x-data="tallstackui_stats(@js($number), @js($animated))"
     x-intersect:enter.full="visible = true"
     x-intersect:leave="visible = false; start = 0"
     x-cloak>
    @if ($header)
        @if ($header instanceof \Illuminate\View\ComponentSlot)
            {{ $header }}
        @else
            <p class="{{ $personalize['slots.header'] }}">{{ $header }}</p>
        @endif
    @endif
    <div @class([
            'mx-2' => !$slot->isNotEmpty(),
            'mt-4' => !$header, 
            'mb-4' => !$footer, 
            $personalize['wrapper.second'],
        ])>
        @if ($icon)
            <div @class([$personalize['wrapper.third'], $colors['background']])>
                <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                     :$icon
                                     internal
                                     class="{{ $personalize['icon'] }}" />
            </div>
        @endif
        <div class="flex-grow">
            @if ($title) <h2 class="{{ $personalize['title'] }}">{{ $title }}</h2> @endif
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @else
                <h2 class="{{ $personalize['number'] }}" x-ref="number">{{ $number }}</h2>
            @endif
        </div>
        @if ($right)
            {{ $right }}
        @elseif ($increase || $decrease)
            <div>
                @if ($increase)
                    <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                         :icon="TallStackUi::icon($personalize['slots.right.increase.icon'])"
                                         internal
                                         class="{{ $personalize['slots.right.increase.class'] }}" />
                @else
                    <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                         :icon="TallStackUi::icon($personalize['slots.right.decrease.icon'])"
                                         internal
                                         class="{{ $personalize['slots.right.decrease.class'] }}" />
                @endif
            </div>
        @endif
    </div>
    @if ($footer)
        @if ($footer instanceof \Illuminate\View\ComponentSlot)
            {{ $footer }}
        @else
            <p class="{{ $personalize['slots.footer'] }}">{{ $footer }}</p>
        @endif
    @endif
</{{ $tag }}>
