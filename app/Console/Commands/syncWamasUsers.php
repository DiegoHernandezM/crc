<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\Associate;
use App\Models\Subarea;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class syncWamasUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crc:wamas_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza usuarios de wamas en redis';

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
        $associates = Associate::where('area_id', Area::SORTER)->get();
        foreach ($associates as $key => $associate) {
            if ($associate->wamas_user != null) {
                $subarea = Subarea::find($associate->subarea_id);
                Redis::set('wamasusers:'.$associate->wamas_user, $associate->id);
                Redis::set('wamasusers:'.$associate->wamas_user.':subarea', $subarea->name);
            }
        }
        return 0;
    }
}
