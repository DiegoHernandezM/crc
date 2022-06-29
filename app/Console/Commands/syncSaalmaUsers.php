<?php

namespace App\Console\Commands;

use App\Models\Associate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class syncSaalmaUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crc:saalma_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza redis con los usuarios de SAALMA';

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
        $associates = Associate::where('area_id', 1)->get();
        foreach ($associates as $key => $asso) {
            Redis::set('saalmausers:'.$asso->user_saalma, $asso->id);
        }
        return 0;
    }
}
