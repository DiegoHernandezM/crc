<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssociateStoreRequest;
use App\Models\Associate;
use App\Models\AssociateSubarea;
use App\Models\Subarea;
use App\Repositories\AssociatesRepository;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class AssociateController extends Controller
{

    public function __construct()
    {
        $this->associateRepository = new AssociatesRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AssociateStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AssociateStoreRequest $request)
    {
        try {
            $requestValidated = $request->validated();
            $asso = Associate::create(
                $requestValidated
            );
            if (isset($requestValidated['user_saalma']) && $requestValidated['user_saalma'] != null) {
                Redis::set('saalmausers:' . $asso->user_saalma, $asso->id);
            }
            if (isset($requestValidated['user_saalma']) && $requestValidated['user_saalma'] != null) {
                Redis::set('wamasusers:' . $asso->wamas_user, $asso->id);
            }
            return ApiResponses::okObject($asso);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Trae información de un asociado.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $associate = Associate::withTrashed()->find($id);
            return ApiResponses::okObject($associate);
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Actualiza asociado.
     *
     * @param  App\Http\Requests\AssociateStoreRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AssociateStoreRequest $request, $id)
    {
        $this->authorize('Asociados.Actualizar');
        $associate = Associate::withTrashed()->find($id);
        $requestValidated = $request->validated();
        if ($associate) {
            if ($requestValidated['subarea_id'] != $associate->subarea_id) {
                $this->associateRepository->updateSubarea($associate, $requestValidated['subarea_id']);
            }
            $associate->update(
                $requestValidated
            );
        }
        if ($requestValidated['user_saalma']) {
            Redis::set('saalmausers:' . $associate->user_saalma, $associate->id);
        }

        if ($requestValidated['wamas_user']) {
            Redis::set('wamasusers:' . $associate->wamas_user, $associate->id);
        }

        return ApiResponses::okObject($associate);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('Asociados.Borrar');
        $associate = Associate::find($id);
        $associate->delete();
        return ApiResponses::okObject($associate);
    }

    /**
     * Restaura el asociado.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $this->authorize('Asociados.Borrar');
        $associate = Associate::withTrashed()->find($id);
        $associate->restore();
        return ApiResponses::okObject($associate);
    }

    public function getAssociates(Request $oRequest)
    {
        $associates = Associate::where(function ($q) use ($oRequest) {
            if (!auth()->user()->isSuperUser()) {
                $q->where('associates.area_id', Auth::user()->area_id);
            }
            if (isset($oRequest->subarea)) {
                if ($oRequest->subarea > 0) {
                    $q->where('associates.subarea_id', $oRequest->subarea);
                }
            }
            if (isset($oRequest->shift)) {
                if ($oRequest->shift > 0) {
                    $q->where('associates.shift_id', $oRequest->shift);
                }
            }
            return $q;
        })
            ->where(
                function ($q) use ($oRequest) {
                    if ($oRequest->search !== false) {
                        return $q
                            ->orWhere('associates.name', 'like', "%$oRequest->search%");
                    }
                }
            )
            ->join('areas', 'areas.id', '=', 'associates.area_id')
            ->join('subareas', 'subareas.id', '=', 'associates.subarea_id')
            ->leftJoin('shifts', 'shifts.id', '=', 'associates.shift_id')
            ->select(
                'associates.id',
                'associates.name',
                'associates.employee_number',
                'associates.entry_date',
                'associates.unionized',
                'areas.name as area',
                'subareas.name as subarea',
                'shifts.name as shift',
                'associates.deleted_at'
            )
            ->orderBy('id', 'desc');
        return response()->json($oRequest->trashed === "true" ? $associates->onlyTrashed()->get() :  $associates->get());
    }

    /**
     * Mueve a un equipo de turno / subarea
     *
     * @param  Illuminate\Http\Request $oRequest
     * @return void
     */
    public function moveTeam(Request $oRequest)
    {
        $this->authorize('Asociados.Actualizar', 'web');
        try {
            if (isset($oRequest->associateIds)) {
                $subarea = Subarea::find($oRequest->newArea);
                $associateModels = Associate::whereIn('id', $oRequest->associateIds)->get();
                foreach ($associateModels as $key => $aModel) {
                    if ($aModel->subarea_id != $oRequest->newArea) {
                        $this->associateRepository->updateSubarea($aModel, $oRequest->newArea);
                    }

                    $aModel->subarea_id = $oRequest->newArea;
                    $aModel->shift_id = $oRequest->newShift;
                    $aModel->save();

                    Redis::set('wamasusers:' . $aModel->wamas_user . ':subarea', $subarea->name);
                }
                return response()->json(['success' => true, 'message' => 'Colaboradores movidos a ' . strtoupper($subarea->name)]);
            } else {
                return response()->json(['success' => false, 'message' => 'No se seleccionó ningún colaborador para mover.']);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }

    /**
     * Encuentra asociado por numero de empleado.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function employee(Request $request)
    {
        $employee = Associate::where('employee_number', strip_tags(trim($request->number)))->withTrashed()->first();
        if (empty($employee)) {
            return ApiResponses::okObject(["success" => true, "message" => ""]);
        } else {
            return ApiResponses::okObject(["success" => false, "message" => "El empleado ya se encuentra registrado"]);
        }
    }
}
