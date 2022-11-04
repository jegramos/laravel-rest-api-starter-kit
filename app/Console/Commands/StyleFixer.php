<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandBase;
use Throwable;

class StyleFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixer:style
                           {--i|ide_helper : Run style fixer with barryvdh/laravel-ide-helper}
                           {--c|check      : Run the style fixer without changing the files}';

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
        $fixer_args = $this->option('check') ? ['--dry-run' => true, '--diff' => true] : [];
        $commands = [
            ['cmd' => 'fixer:fix', 'args' => $fixer_args]
        ];

        if ($this->option('ide_helper')) {
            $commands[] = ['cmd' => 'ide-helper:generate', 'args' => []];
            $commands[] = ['cmd' => 'ide-helper:meta', 'args' => []];
            $commands[] = ['cmd' => 'ide-helper:models', 'args' => ['--nowrite' => true]];
        }

        $this->info('🧹 Cleaning up your dirty code...');

        $exitCode = CommandBase::SUCCESS;
        foreach ($commands as $command) {
            try {
                $this->call($command['cmd'], $command['args']);
            } catch (Throwable $th) {
                $this->error($th->getMessage());
                $exitCode = CommandBase::FAILURE;
            }
        }

        $this->info("🧺 Code cleanup done!" . "yeat");

        return $exitCode;
    }
}
