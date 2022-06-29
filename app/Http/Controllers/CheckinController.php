<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckinRequest;
use App\Models\Associate;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class CheckinController extends Controller
{
    protected $mAssociate;
    protected $mCheckin;

    public function __construct()
    {
        $this->mAssociate = new Associate();
        $this->mCheckin = new Checkin();
    }

    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->authorize('Checador');
        return Inertia::render('Checkin/Index', [
            'check' => $this->getCheckin()
        ]);
    }

    /**
     * @param CheckinRequest $oRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAssociate(CheckinRequest $oRequest)
    {
        //$this->authorize('Checador');
        $associate = $this->mAssociate->where('employee_number', '=', $oRequest->employee_number)->first();
        if (!empty($associate)) {
            $check = $this->mCheckin->where('associate_id', $associate->id)->whereDate('checkin', Carbon::today())->first();

            if (!empty($check) && $check->checkout != null) {
                return response()->json([
                    "associates" => $this->getCheckin(),
                    "associate" => $associate ?? [],
                    'message' => 'El asociado ya ha sido registrado el dia de hoy']);
            } elseif (!empty($check) && $check->checkout === null) {
                if (Carbon::now()->format('Y-m-d H:i:s') < Carbon::parse($check->checkin)->addHour()) {
                    return response()->json([
                        "associates" => $this->getCheckin(),
                        "associate" => $associate ?? [],
                        'message' => 'El asociado aun no puede registrar su salida']);
                }
                $check->checkout = Carbon::now()->format('Y-m-d H:i:s');
                $check->save();
                return response()->json(["associates" => $this->getCheckin(), "associate" => $associate ?? [], 'message' => '']);
            } else {
                $this->mCheckin->create([
                    'associate_id' => $associate->id,
                    'checkin' => Carbon::now()->format('Y-m-d H:i:s'),
                    'checkout' => null,
                    'status' => 1,
                    'user_id' => Auth::user()->id
                ]);
                return response()->json(["associates" => $this->getCheckin(), "associate" => $associate ?? [], 'message' => '']);
            }
        } else {
            return response()->json(["associates" => $this->getCheckin(), "associate" => $associate ?? [], 'message' => 'El número de empleado no se encuentra registrado']);
        }
    }

    /**
     * @return mixed
     */
    private function getCheckin()
    {
        $this->authorize('Checador');
        $checks = $this->mCheckin
            ->join('associates', 'associates.id', '=', 'checkin.associate_id')
            ->join('areas', 'areas.id', '=', 'associates.area_id')
            ->join('shifts', 'shifts.id', '=', 'associates.shift_id')
            ->orderBy('checkin.checkin', 'desc')->limit(5)
            ->where(function ($q) {
                if (Auth::user()->area_id != null) {
                    $q->where('associates.area_id', Auth::user()->area_id);
                }
                return $q;
            })
            ->whereDate('checkin.checkin', Carbon::today())
            ->select(
                'checkin.*',
                'associates.name as associate',
                'associates.employee_number',
                'associates.picture',
                'shifts.name as shift',
                'areas.name as area',
                'areas.id as area_id'
            )
            ->limit(5)
            ->get();

        return $checks;
    }

    public function getAssistencesAssociate(Request $oRequest, $id)
    {
        $this->authorize('Checador');
        $checks = $this->mCheckin->where('associate_id', $id)
            ->where(function ($q) use ($oRequest) {
                $dateInit = ($oRequest->dateInit) ? Carbon::parse($oRequest->dateInit)->format('Y-m-d').' 00:00:00' : null;
                $dateEnd = ($oRequest->dateEnd) ? Carbon::parse($oRequest->dateEnd)->format('Y-m-d').' 23:59:59' : Carbon::now()->format('Y-m-d').' 23:59:59';
                if ($dateInit !== null) {
                    return $q
                        ->wherebetween('checkin', [$dateInit, $dateEnd]);
                }
            })
            ->paginate((int) $oRequest->input('per_page', 25));
        return response()->json(['assistences' => $checks]);
    }

    public function updateAssistence(Request $oRequest)
    {
        $this->authorize('Checador');
        $check = $this->mCheckin->find($oRequest->data['id']);
        if ($check) {
            $check->checkin = $oRequest->data['checkin'];
            $check->checkout = $oRequest->data['checkout'];
            $check->comments = $oRequest->comments;
            $check->user_id = Auth::user()->id;
            $check->save();
            return response()->json(['message' => 'ok', 'assistence' => $check]);
        }
        return response()->json(['message' => 'error', 'assistence' => []]);
    }

    public function store(Request $oRequest)
    {
        $this->authorize('Checador');
        $check = $this->mCheckin->where('associate_id', $oRequest->data['id'])->whereDate('checkin', Carbon::parse($oRequest->data['checkin'])->format('Y-m-d'))->first();
        if (empty($check)) {
            $checkin = $this->mCheckin->create([
                'created_at' => Carbon::parse($oRequest->data['checkin'])->format('Y-m-d').' 00:00:00',
                'associate_id' => $oRequest->data['id'],
                'status' => true,
                'user_id' => Auth::user()->id,
                'checkin' => $oRequest->data['checkin'],
                'checkout' => $oRequest->data['checkout'],
            ]);
            return response()->json(['message' => 'ok', 'assistence' => $checkin]);
        }
        return response()->json(['message' => 'El asistente ya tiene registro hoy', 'assistence' => []]);
    }
}
