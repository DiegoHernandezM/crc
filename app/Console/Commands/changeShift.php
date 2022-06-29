<?php

namespace App\Console\Commands;

use App\Repositories\ChangeShiftRepository;
use Illuminate\Console\Command;

class changeShift extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crc:change_shift';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cambio de horario automatico para area de sorter';

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
        $repository = new ChangeShiftRepository();
        $change = $repository->changeShift();
        return true;
    }
}
