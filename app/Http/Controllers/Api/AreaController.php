<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\AreaRequest;
use App\Repositories\AreaRepository;
use Illuminate\Http\Request;

class AreaController extends Controller
{

    public function index(Request $request, AreaRepository $rArea)
    {
        $areas = $rArea->getAllAreas($request);
        return ApiResponses::okObject($areas);
    }

    public function store(AreaRequest $request, AreaRepository $rArea)
    {
        try {
            return ApiResponses::okObject($rArea->createArea($request));
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function edit($id, AreaRepository $rArea)
    {
        $area = $rArea->getArea($id);
        if ($area) {
            return ApiResponses::okObject($area);
        }
        return ApiResponses::notFound();
    }

    public function update($id, Request $request, AreaRepository $rArea)
    {
        try {
            $area = $rArea->updateArea($id, $request);
            return ApiResponses::okObject($area);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function destroy($id, AreaRepository $rArea)
    {
        try {
            $area = $rArea->destroyArea($id);
            return ApiResponses::ok('Area eliminada');
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function restore($id, AreaRepository $rArea)
    {
        try {
            $area = $rArea->restoreArea($id);
            return ApiResponses::okObject($area);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }
}
