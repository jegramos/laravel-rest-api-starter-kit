<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectInit extends Command
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
    protected $description = 'Initialize the project. Generate app key, run migrations, seeders, etc.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->call('key:generate');
        $this->call('migrate:refresh');
        $this->call('db:seed');
        $this->call('app:styler', ['--ide_helper' => true]);
        return Command::SUCCESS;
    }
}
