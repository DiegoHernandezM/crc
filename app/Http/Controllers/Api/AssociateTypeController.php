<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssociateTypeRequest;
use App\Repositories\AssociateTypeRepository;
use Illuminate\Http\Request;

class AssociateTypeController extends Controller
{

    public function index(Request $request, AssociateTypeRepository $rAssociateType)
    {
        $associateType = $rAssociateType->getAll($request);
        return ApiResponses::okObject($associateType);
    }

    public function store(AssociateTypeRequest $request, AssociateTypeRepository $rAssociateType)
    {
        try {
            $associateType = $rAssociateType->createAssociateType($request);
            return ApiResponses::okObject($associateType);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function edit($id, AssociateTypeRepository $rAssociateType)
    {
        $associateType = $rAssociateType->getAssociateType($id);
        if ($associateType) {
            return ApiResponses::okObject($associateType);
        }
        return ApiResponses::notFound();
    }

    public function update($id, AssociateTypeRequest $request, AssociateTypeRepository $rAssociateType)
    {
        try {
            $associateType = $rAssociateType->updateAssociateType($id, $request);
            return ApiResponses::okObject($associateType);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function destroy($id, AssociateTypeRepository $rAssociateType)
    {
        try {
            $rAssociateType->destroyAssociateType($id);
            return ApiResponses::ok('Tipo de asociado eliminado');
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function restore($id, AssociateTypeRepository $rAssociateType)
    {
        try {
            $associateType = $rAssociateType->restoreAssociateType($id);
            return ApiResponses::okObject($associateType);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }
}
