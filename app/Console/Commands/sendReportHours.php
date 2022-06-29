<?php

namespace App\Console\Commands;

use App\Managers\SendMailsManager;
use Illuminate\Console\Command;

class sendReportHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:hours-picking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte semanal de horas extra de asociados';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $report = new SendMailsManager();
        $send = $report->sendMailReportHoursPicking();
        if ($send) {
            return true;
        } else {
            return false;
        }
    }
}
