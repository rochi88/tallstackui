@php
    [$property, $error, $id, $entangle] = $bind($attributes, $errors ?? null, $livewire);
    $personalize = $classes();
    $icon = $icons();
    $attributes = $mergeWhen($rules->isNotEmpty(), ['x-on:click' => 'rules = true', 'x-model.debounce' => 'input']);
@endphp

<div x-data="tallstackui_formPassword({!! $entangle !!}, @js($rules), @js($generator))" class="relative" x-cloak x-on:click.outside="rules = false">
     <x-dynamic-component :component="TallStackUi::component('input')"
                          {{ $attributes->except('autocomplete') }}
                          :$label
                          :$hint
                          :$invalidate
                          ::type="!show ? 'password' : 'text'"
                          floatable
                          autocomplete="{{ $attributes->get('autocomplete', 'off') }}"
                          x-on:keydown="caps = $event.getModifierState('CapsLock')"
                          x-on:keyup="caps = $event.getModifierState('CapsLock')">
         <x-slot:suffix>
             <div @class([$personalize['icon.wrapper'], 'justify-between gap-2']) x-cloak>
                 @if (!$mixedCase)
                 <div x-show="caps" dusk="tallstackui_form_password_capslock">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 256 256">
                        <rect width="256" height="256" fill="none"/>
                        <polygon points="32 120 128 24 224 120 176 120 176 184 80 184 80 120 32 120" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
                        <line x1="176" y1="216" x2="80" y2="216" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/>
                    </svg>
                 </div>
                 @endif
                 @if ($generator)
                     <button type="button" x-ref="generator" dusk="tallstackui_form_password_generate" x-on:click="generator(); show = true;" {!! $attributes->only('x-on:generate') !!}>
                         <x-dynamic-component :component="TallStackUi::component('icon')"
                                              :icon="TallStackUi::icon('arrow-path')"
                                              :$error
                                 @class($personalize['icon.class']) />
                     </button>
                 @endif
                 <button type="button"
                         dusk="tallstackui_form_password_reveal"
                         {{ $attributes->only('x-on:reveal') }}
                         x-on:click="toggle()">
                         <x-dynamic-component :component="TallStackUi::component('icon')"
                                              :icon="TallStackUi::icon('eye')"
                                              :$error
                                              x-show="!show"
                                              @class($personalize['icon.class']) />
                         <x-dynamic-component :component="TallStackUi::component('icon')"
                                              :icon="TallStackUi::icon('eye-slash')"
                                              :$error
                                              x-show="show"
                                              @class($personalize['icon.class']) />
                 </button>
             </div>
         </x-slot:suffix>
     </x-dynamic-component>
    @if ($rules->isNotEmpty())
        <x-dynamic-component :component="TallStackUi::component('floating')"
                             class="w-full p-3"
                             x-show="rules">
            <h3 @class($personalize['rules.title'])>{{ __('tallstack-ui::messages.password.rules.title') }}</h3>
            <div @class($personalize['rules.block'])>
                @if ($rules->has('min'))
                    <span @class($personalize['rules.items.base'])>
                            <x-dynamic-component :component="TallStackUi::component('icon')"
                                                 :icon="$icon['x-circle']"
                                                 :class="$personalize['rules.items.icons.error']"
                                                 x-show="!results.min" />
                            <x-dynamic-component :component="TallStackUi::component('icon')"
                                                 :icon="$icon['check-circle']"
                                                 :class="$personalize['rules.items.icons.success']"
                                                 x-show="results.min" />
                            <p x-bind:class="{ 'line-through' : results.min }">{{ __('tallstack-ui::messages.password.rules.formats.min', ['min' => $rules->get('min')]) }}</p>
                        </span>
                @endif
                @if ($rules->has('symbols'))
                    <span @class($personalize['rules.items.base'])>
                            <x-dynamic-component :component="TallStackUi::component('icon')"
                                                 :icon="$icon['x-circle']"
                                                 :class="$personalize['rules.items.icons.error']"
                                                 x-show="!results.symbols" />
                            <x-dynamic-component :component="TallStackUi::component('icon')"
                                                 :icon="$icon['check-circle']"
                                                 :class="$personalize['rules.items.icons.success']"
                                                 x-show="results.symbols" />
                            <p x-bind:class="{ 'line-through' : results.symbols }">{{ __('tallstack-ui::messages.password.rules.formats.symbols', ['symbols' => $rules->get('symbols')]) }}</p>
                        </span>
                @endif
                @if ($rules->has('numbers'))
                    <span @class($personalize['rules.items.base'])>
                            <x-dynamic-component :component="TallStackUi::component('icon')"
                                                 :icon="$icon['x-circle']"
                                                 :class="$personalize['rules.items.icons.error']"
                                                 x-show="!results.numbers" />
                            <x-dynamic-component :component="TallStackUi::component('icon')"
                                                 :icon="$icon['check-circle']"
                                                 :class="$personalize['rules.items.icons.success']"
                                                 x-show="results.numbers" />
                            <p x-bind:class="{ 'line-through' : results.numbers }">{{ __('tallstack-ui::messages.password.rules.formats.numbers') }}</p>
                        </span>
                @endif
                @if ($rules->has('mixed'))
                    <span @class($personalize['rules.items.base'])>
                            <x-dynamic-component :component="TallStackUi::component('icon')"
                                                 :icon="$icon['x-circle']"
                                                 :class="$personalize['rules.items.icons.error']"
                                                 x-show="!results.mixed" />
                            <x-dynamic-component :component="TallStackUi::component('icon')"
                                                 :icon="$icon['check-circle']"
                                                 :class="$personalize['rules.items.icons.success']"
                                                 x-show="results.mixed" />
                            <p x-bind:class="{ 'line-through' : results.mixed }">{{ __('tallstack-ui::messages.password.rules.formats.mixed') }}</p>
                        </span>
                @endif
            </div>
        </x-dynamic-component>
    @endif
</div>
