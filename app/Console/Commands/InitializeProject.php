<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitializeProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set-up the entire project. Run migrations, seeders, etc.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->call('key:generate');
        $this->call('app:styler', ['-i' => true]);
        $this->call('migrate:fresh');
        $this->call('db:seed');

        return Command::SUCCESS;
    }
}
