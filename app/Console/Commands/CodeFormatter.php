<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;

class CodeFormatter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:styler
                           {--i|ide_helper : Run style fixer with barryvdh/laravel-ide-helper}
                           {--t|test       : Run the style fixer without changing the files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs a global code formatter that follows PHP PSR standards';

    /**
     * Execute the console command.
     *
     * @see https://github.com/stechstudio/Laravel-PHP-CS-Fixer
     * @see https://github.com/barryvdh/laravel-ide-helper
     */
    public function handle(): int
    {
        $pintTestArg = $this->option('test') ? '--test' : '';
        $pintCommand = 'vendor/bin/pint '.$pintTestArg;

        $this->info("\u{1F9F9} Cleaning up your dirty code...");
        $exitCode = Command::SUCCESS;

        exec($pintCommand, $output, $exitCode);

        foreach ($output as $message) {
            echo $message.PHP_EOL;
        }

        $ideHelperCommands = [];
        if ($this->option('ide_helper')) {
            $ideHelperCommands[] = ['cmd' => 'ide-helper:generate', 'args' => []];
            $ideHelperCommands[] = ['cmd' => 'ide-helper:meta', 'args' => []];
            $ideHelperCommands[] = ['cmd' => 'ide-helper:models', 'args' => ['--nowrite' => true]];
        }

        foreach ($ideHelperCommands as $ideHelperCommand) {
            try {
                $this->call($ideHelperCommand['cmd'], $ideHelperCommand['args']);
            } catch (Throwable $th) {
                $this->error("\u{1F645}  Error: ".$th->getMessage());
                $exitCode = Command::FAILURE;
            }
        }

        $this->info("\u{1F9FA} Code cleanup done!");

        // Add changes to Git if success
        if ($exitCode === Command::SUCCESS) {
            exec('git add .');
        }

        return $exitCode;
    }
}
