@php
    $personalize = $classes();
@endphp

<div @class($personalize['wrapper.first'])
     x-data="tallstackui_dropdown(@js(!$static))"
     role="button" 
     aria-haspopup="true" 
     x-bind:aria-expanded="show">
    <div @class($personalize['wrapper.second']) x-on:click.outside="show = false" x-ref="dropdown">
        @if ($text)
            <div @class($personalize['action.wrapper'])>
                <span @class($personalize['action.text'])>{{ $text }}</span>
                <button type="button" 
                        x-on:click="show = !show"
                        aria-controls="dropdown-menu"
                        dusk="tallstackui_open_dropdown">
                    <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                         :icon="TallStackUi::icon('chevron-down')"
                                         internal
                                         class="{{ $personalize['action.icon'] }}"
                                         x-bind:class="{ 'transform rotate-180': animate && show }"/>
                </button>
            </div>
        @elseif ($icon)
            <div @class($personalize['action.wrapper'])>
                <button type="button" 
                        x-on:click="show = !show"
                        aria-controls="dropdown-menu"
                        dusk="tallstackui_open_dropdown">
                    <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                         :$icon
                                         internal
                                         class="{{ $personalize['action.icon'] }}"
                                         x-bind:class="{ 'transform rotate-180': animate && show }" />
                </button>
            </div>
        @else
            {!! $action !!}
        @endif
        <x-dynamic-component :component="TallStackUi::prefix('floating')"
                             :floating="$personalize['floating']"
                             offset="5"
                             :$position
                             x-anchor="$refs.dropdown"
                             role="menu">
            <x-slot:transition>
                {!! $transitions() !!}
            </x-slot:transition>
            @if ($header)
                <div @class($personalize['header.wrapper'])>
                    {!! $header !!}
                </div>
            @endif
            <div @class($personalize['slot.wrapper'])>
                {!! $slot !!}
            </div>
        </x-dynamic-component>
    </div>
</div>
