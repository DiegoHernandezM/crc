<?php

namespace App\Repositories;

use App\Models\UserReport;

class UserReportRepository
{
    protected $mUserReport;

    public function __construct()
    {
        $this->mUserReport = new UserReport();
    }

    public function getUsersMails()
    {
        try {
            $users = $this->mUserReport->all();
            return $users;
        } catch (\Exception $e) {
            return $e;
        }
    }
}