<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Repositories\ExtraHoursRepository;
use Illuminate\Http\Request;

class ExtraHoursController extends Controller
{
    public function getHours(Request $request, ExtraHoursRepository $rExtraHours)
    {
        try {
            return ApiResponses::okObject($rExtraHours->getDataHours($request));
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }
}
