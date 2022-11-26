<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;

class StyleFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:styler
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

        $this->info("\u{1F9F9} Cleaning up your dirty code...");

        $exitCode = Command::SUCCESS;
        foreach ($commands as $command) {
            try {
                $this->call($command['cmd'], $command['args']);
            } catch (Throwable $th) {
                $this->error("\u{1F645}  Error: " . $th->getMessage());
                $exitCode = Command::FAILURE;
            }
        }

        $this->info("\u{1F9FA} Code cleanup done!");

        return $exitCode;
    }
}
