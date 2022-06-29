<?php

namespace App\Console\Commands;

use App\Managers\SendMailsManager;
use Illuminate\Console\Command;

class sendReportProdStaffSorter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ccr:productivity_staff_sorter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa la productividad de staff y managers de sorter';

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
        $send = $report->sendMailProductivityStaffSorter();
        if ($send) {
            return true;
        } else {
            return false;
        }
    }
}
