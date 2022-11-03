<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandBase;

class StyleFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixer:style
                           {--I|ide_helper : Run style fixer with barryvdh/laravel-ide-helper}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs a global code formatter that follows PHP PSR standards';

    /**
     * Execute the console command.
     * @see https://github.com/stechstudio/Laravel-PHP-CS-Fixer
     * @see https://github.com/barryvdh/laravel-ide-helper
     *
     * @return int
     */
    public function handle(): int
    {
        $commands = [
            ['cmd' => 'fixer:fix', 'args' => []]
        ];

        if ($this->option('ide_helper')) {
            $commands[] = ['cmd' => 'ide-helper:generate', 'args' => []];
            $commands[] =  ['cmd' => 'ide-helper:meta', 'args' => []];
            $commands[] = ['cmd' => 'ide-helper:models', 'args' => ['--nowrite' => true]];
        }

        $this->info('🧹 Cleaning up your dirty code...');
        foreach ($commands as $command) {
            $this->call($command['cmd'], $command['args']);
        }
        $this->info("🧺 Code cleanup done!");

        return CommandBase::SUCCESS;
    }
}
