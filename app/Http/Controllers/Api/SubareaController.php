<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubareaRequest;
use App\Models\Subarea;
use App\Repositories\SubareaRepository;
use Illuminate\Http\Request;

class SubareaController extends Controller
{

    public function show(Request $request, SubareaRepository $rSubarea)
    {
        $subareas = $rSubarea->getAll($request);
        return ApiResponses::okObject($subareas);
    }

    public function create(SubareaRequest $request, SubareaRepository $rSubarea)
    {
        try {
            $subarea = $rSubarea->createSubarea($request);
            return ApiResponses::okObject($subarea);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function edit($id, SubareaRepository $rSubarea)
    {
        $subarea = $rSubarea->getSubarea($id);
        if ($subarea) {
            return ApiResponses::okObject($subarea);
        }
        return ApiResponses::notFound();
    }

    public function update($id, Request $request, SubareaRepository $rSubarea)
    {
        try {
            $subarea = $rSubarea->updateSubarea($id, $request);
            return ApiResponses::okObject($subarea);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function destroy($id, SubareaRepository $rSubarea)
    {
        try {
            $rSubarea->destroySubarea($id);
            return ApiResponses::ok('Subarea eliminada');
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function restore($id, SubareaRepository $rSubarea)
    {
        try {
            $subarea = $rSubarea->restoreSubarea($id);
            return ApiResponses::okObject($subarea);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }


    /**
     * Obtiene subareas cuando no se proporciona un area especifica
     *
     * @return App\Http\Controllers\ApiResponses
     */
    public function getFromArea()
    {
        try {
            $subareas = Subarea::where(
                fn ($q) =>
                auth()->user()->isSuperUser() ? $q : $q->where('area_id', auth()->user()->area_id)
            )->get();
            return ApiResponses::okObject($subareas);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }
}
