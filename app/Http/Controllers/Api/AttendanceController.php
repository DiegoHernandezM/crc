<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function all(Request $request, AttendanceRepository $rAttendance)
    {
        try {
            return ApiResponses::okObject($rAttendance->getAll($request));
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function historic(Request $request, AttendanceRepository $rAttendance)
    {
        try {
            return $rAttendance->getHistoric($request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function historicAssociate($id, Request $request, AttendanceRepository $rAttendance)
    {
        try {
            return $rAttendance->getHistoricAssociate($id, $request);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }
}
