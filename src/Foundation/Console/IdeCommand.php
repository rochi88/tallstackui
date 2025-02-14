<?php

namespace TallStackUi\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class IdeCommand extends Command
{
    protected $description = 'Generate IDE configuration file for TallStackUI components.';

    protected $signature = 'tallstackui:ide';

    public function handle(): int
    {
        $json = ['$schema' => 'https://laravel-ide.com/schema/laravel-ide-v2.json'];

        $list = [];

        $components = config('tallstackui.components');

        if (count($components) === 0) {
            $this->components->error('No TallStackUI components found based on the config file.');

            return self::FAILURE;
        }

        if (file_exists(base_path('ide.json'))) {
            $this->components->warn('The ide.json file already exists. The process will overwrite it.');
        }

        File::delete(base_path('ide.json'));

        foreach (config('tallstackui.components') as $name => $class) {
            $list[] = [
                'name' => $name,
                'className' => $class,
            ];
        }

        $json['blade'] = [
            'components' => [
                'list' => $list,
            ],
        ];

        file_put_contents(base_path('ide.json'), json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->components->info('The ide.json file was successfully generated. Rebuild your Laravel Idea metadata.');

        return self::SUCCESS;
    }
}
