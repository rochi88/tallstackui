<?php

namespace TallStackUi\View\Components\Form\Select\Traits;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use TallStackUi\View\Components\Form\Select\Native;

trait Setup
{
    protected function setup(): void
    {
        $this->options = $this->options instanceof Collection
            ? $this->options->values()->toArray()
            : array_values($this->options);

        $this->select ??= 'label:label|value:value|description:description|image:image';

        if (! $this->select || ($this->options !== [] && ! is_array($this->options[0]))) {
            return;
        }

        $select = array_reduce(
            explode('|', $this->select),
            function ($result, $item) {
                $parts = explode(':', $item, 2);
                if (count($parts) === 2) {
                    $result[$parts[0]] = $parts[1];
                }

                return $result;
            },
            []
        );

        // $label is actually the name of the label, and not the label itself. The same
        // happens to the $value, is the name of the value and not the value itself.
        $label = $select['label'] ?? 'label';
        $value = $select['value'] ?? 'value';
        $description = $select['description'] ?? null;
        $image = $select['image'] ?? null;

        $component = $this instanceof Native ? 'select.native' : 'select.styled';

        $images = array_flip(['image', 'img', 'img_src']);
        $descriptions = array_flip(['description', 'note']);

        $this->options = collect($this->options)
            ->map(function (array $item) use ($label, $value, $description, $image, $component, $images, $descriptions): array {
                if (! array_key_exists($label, $item)) {
                    throw new InvalidArgumentException("The $component key [$label] is missing in the options array.");
                }

                if (! array_key_exists($value, $item)) {
                    throw new InvalidArgumentException("The $component [$value] is missing in the options array.");
                }

                $this->grouped = is_array($item[$value]);

                return [
                    $label => $item[$label],
                    $value => $item[$value],
                    $image ?? 'image' => current(array_intersect_key($item, $images)) ?: null,
                    'disabled' => $item['disabled'] ?? false,
                    $description ?? 'description' => $description ? $item[$description] : (current(array_intersect_key($item, $descriptions)) ?: null),
                ];
            })
            ->toArray();

        $this->selectable = ['label' => $label, 'value' => $value, 'description' => $description ?? 'description', 'image' => $image ?? 'image'];
    }
}
