<?php

namespace App\Managers;

use App\Models\Log as Logger;
use App\Repositories\AssociatesRepository;
use Carbon\Carbon;

class AssociatesManager
{

    protected $cAssociateRepo;

    public function __construct()
    {
        $this->cAssociateRepo = new AssociatesRepository();
    }

    public function validateEntryAssocite()
    {
        try {

            $picking = Carbon::now()->subMonths(1)->format('Y-m-d');
            $sorter = Carbon::now()->subMonths(1)->format('Y-m-d');
            return $this->cAssociateRepo->verifyEntry($picking, $sorter);

        } catch (\Exception $e) {
            $logData = [
                'message'       => $e->getMessage(),
                'loggable_id'   => Logger::LOG_SYSTEM,
                'loggable_type' => 'Sistem',
                'user_id'       => 1,
            ];
            $log = new Logger();
            $log->create($logData);
        }

    }
}