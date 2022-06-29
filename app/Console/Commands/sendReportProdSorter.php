<?php

namespace App\Console\Commands;

use App\Managers\SendMailsManager;
use Illuminate\Console\Command;

class sendReportProdSorter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crc:productivity_sorter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia la productividad del sorter';

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
        $send = $report->sendMailProductivitySorter();
        if ($send) {
            return true;
        } else {
            return false;
        }
    }
}
