<?php

namespace App\Console\Commands;

use App\Managers\AssociatesManager;
use Illuminate\Console\Command;

class statusElegibleAssociate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crc:validate_entry_date_associates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica que el asociado pueda ser elegido para bonos en sorter y picking';

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
        $manager = new AssociatesManager();
        $validate = $manager->validateEntryAssocite();
        if ($validate) {
            return true;
        } else {
            return false;
        }
    }
}
