<?php

namespace TallStackUi\Foundation\Colors\Stubs;

class DialogColors
{
    protected function cancelColor(): ?string
    {
        return null;
    }

    protected function confirmColor(): array
    {
        return [
            'success' => null,
            'error' => null,
            'info' => null,
            'warning' => null,
            'question' => null,
        ];
    }

    protected function iconColor(): array
    {
        return [
            'background' => [
                'success' => null,
                'error' => null,
                'info' => null,
                'warning' => null,
                'question' => null,
            ],
            'icon' => [
                'success' => null,
                'error' => null,
                'info' => null,
                'warning' => null,
                'question' => null,
            ],
        ];
    }
}
