<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShiftRequest;
use App\Repositories\ShiftRepository;
use Illuminate\Http\Request;

class ShiftController extends Controller
{

    public function show(Request $request, ShiftRepository $rShift)
    {
        $shifts = $rShift->getAll($request);
        return ApiResponses::okObject($shifts);
    }

    public function create(ShiftRequest $request, ShiftRepository $rShift)
    {
        try {
            $shift = $rShift->createShift($request);
            return ApiResponses::okObject($shift);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function edit($id, ShiftRepository $rShift)
    {
        $shift = $rShift->getShift($id);
        if ($shift) {
            return ApiResponses::okObject($shift);
        }
        return ApiResponses::notFound();
    }

    public function update($id, ShiftRequest $request, ShiftRepository $rShift)
    {
        try {
            $shift = $rShift->updateShift($id, $request);
            return ApiResponses::okObject($shift);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function destroy($id, ShiftRepository $rShift)
    {
        try {
            $rShift->destroyShift($id);
            return ApiResponses::ok('Horario eliminado');
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function restore($id, ShiftRepository $rShift)
    {
        try {
            $shift = $rShift->restoreShift($id);
            return ApiResponses::okObject($shift);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function showByArea($area, ShiftRepository $rShift)
    {
        try {
            $shifts = $rShift->getByArea($area);
            return ApiResponses::okObject($shifts);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }
}
