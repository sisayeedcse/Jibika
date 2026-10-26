<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DemoResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database and re-seed all categories and demo story data in demo mode';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!config('app.demo_mode')) {
            $this->error('demo:reset is only permitted when DEMO_MODE=true.');
            return Command::FAILURE;
        }

        $this->info('Resetting JIBIKA demo environment...');

        $exitCode = Artisan::call('migrate:fresh', [
            '--seed'  => true,
            '--force' => true,
        ]);

        $this->output->write(Artisan::output());

        if ($exitCode === 0) {
            $this->info('JIBIKA demo environment reset successfully with seeded story personas.');
            return Command::SUCCESS;
        }

        $this->error('Failed to reset demo environment.');
        return Command::FAILURE;
    }
}
