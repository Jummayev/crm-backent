<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DebugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run debug command and display system information';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        dd(config());
        $this->info('🚀 Debug Information');
        $this->newLine();

        $this->comment('System Information:');
        $this->line('PHP Version: '.PHP_VERSION);
        $this->line('Laravel Version: '.app()->version());
        $this->line('Environment: '.app()->environment());
        $this->line('Debug Mode: '.(config('app.debug') ? 'Enabled' : 'Disabled'));

        $this->newLine();
        $this->comment('Database:');
        $this->line('Connection: '.config('database.default'));
        $this->line('Database: '.config('database.connections.'.config('database.default').'.database'));

        $this->newLine();
        $this->comment('Modules:');
        $modules = \Nwidart\Modules\Facades\Module::all();
        foreach ($modules as $module) {
            $status = $module->isEnabled() ? '✅' : '❌';
            $this->line($status.' '.$module->getName());
        }

        $this->newLine();
        $this->info('✅ Debug command executed successfully!');

        return Command::SUCCESS;
    }
}
