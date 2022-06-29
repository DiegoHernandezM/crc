<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckinRequest;
use App\Repositories\CheckinRepository;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function index(CheckinRepository $rCheckin)
    {
        try {
            return ApiResponses::okObject($rCheckin->lastCheckin());
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function getAssistsAssociate($id, Request $request, CheckinRepository $rCheckin)
    {
        try {
            return ApiResponses::okObject($rCheckin->getCheckinAssociate($id, $request));
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function checkAssociate(CheckinRequest $request, CheckinRepository $rCheckin)
    {
        try {
            return ApiResponses::okObject($rCheckin->registerCheck($request));
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function update($id, Request $request, CheckinRepository $rCheckin)
    {
        try {
            $checkin = $rCheckin->updateCheckin($id, $request);
            return ApiResponses::okObject($checkin);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function store(Request $request, CheckinRepository $rCheckin)
    {
        try {
            $checkin = $rCheckin->createCheckin($request);
            if (!$checkin) {
                return ApiResponses::found('El colaborador ya cuenta con un registro el dia seleccionado');
            }
            return ApiResponses::okObject($checkin);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }
}
