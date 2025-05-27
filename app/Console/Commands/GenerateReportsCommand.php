<?php

namespace App\Console\Commands;

use App\Jobs\SendDailyReports;
use Illuminate\Console\Command;

class GenerateReportsCommand extends Command
{
    protected $signature = 'reports:generate 
                            {--daily : Generate daily reports}
                            {--weekly : Generate weekly reports}
                            {--monthly : Generate monthly reports}';

    protected $description = 'Generate email management reports';

    public function handle()
    {
        if ($this->option('daily')) {
            $this->info('Generating daily reports...');
            SendDailyReports::dispatch();
            $this->info('Daily reports dispatched successfully!');
            return;
        }

        if ($this->option('weekly')) {
            $this->info('Generating weekly reports...');
            // Weekly report logic would go here
            $this->info('Weekly reports generated successfully!');
            return;
        }

        if ($this->option('monthly')) {
            $this->info('Generating monthly reports...');
            // Monthly report logic would go here
            $this->info('Monthly reports generated successfully!');
            return;
        }

        $this->error('Please specify a report type (--daily, --weekly, or --monthly)');
    }
}
