<?php


namespace App\Repositories;


use App\Models\Associate;
use App\Models\Checkin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CheckinRepository
{
    protected $mCheckin;
    protected $mAssociate;

    public function __construct()
    {
        $this->mCheckin = new Checkin();
        $this->mAssociate = new Associate();
    }

    public function lastCheckin()
    {
        return $this->mCheckin
            ->whereHas('associate', function ($q) {
                return $q->where(function ($query) {
                    if (Auth::user()->area_id) {
                        return $query->where('associates.area_id', Auth::user()->area_id);
                    }
                });
            })
            ->with(['associate' => function ($q) {
                $q->join('areas', 'areas.id', '=', 'associates.area_id')
                    ->join('shifts', 'shifts.id', '=', 'associates.shift_id')
                    ->select(
                        'associates.id',
                        'associates.employee_number',
                        'associates.name',
                        'shifts.name as shift',
                        'areas.name as area',
                        'areas.id as area_id'
                    );
                return $q;
            }])
            ->orderBy('checkin.updated_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getCheckinAssociate($id, $request)
    {
        return $this->mCheckin
            ->where(function ($q) use ($id, $request) {
                $dateInit = ($request->init) ? Carbon::parse($request->init)->format('Y-m-d').' 00:00:00' : null;
                $dateEnd = ($request->end) ? Carbon::parse($request->end)->format('Y-m-d').' 23:59:59' : Carbon::now()->format('Y-m-d').' 23:59:59';
                if ($dateInit !== null) {
                    $q->wherebetween('checkin', [$dateInit, $dateEnd]);
                }
                $q->where('associate_id', $id);
                return $q;
            })
            ->get();
    }

    public function registerCheck($request)
    {
        $associate = $this->mAssociate->where('employee_number', '=', $request->employee_number)->first();
        if (!empty($associate)) {
            $check = $this->mCheckin->where('associate_id', $associate->id)->whereDate('checkin', Carbon::today())->first();
            if (!empty($check) && $check->checkout != null) {
                $message = 'El asociado ya ha sido registrado el dia de hoy';
            } elseif (!empty($check) && $check->checkout === null) {
                if (Carbon::now()->format('Y-m-d H:i:s') < Carbon::parse($check->checkin)->addHour()) {
                    $message = 'El asociado aun no puede registrar su salida';
                } else {
                    $check->checkout = Carbon::now()->format('Y-m-d H:i:s');
                    $check->save();
                    $message = '';
                }
            } else {
                $this->mCheckin->create([
                    'associate_id' => $associate->id,
                    'checkin' => Carbon::now()->format('Y-m-d H:i:s'),
                    'checkout' => null,
                    'status' => 1,
                    'user_id' => Auth::user()->id
                ]);
                $message = '';
            }
        } else {
            $message = 'El número de empleado no se encuentra registrado';
        }
        return [
            "associates" => $this->lastCheckin(),
            "associate" => $associate ?? [],
            'message' => $message
        ];
    }

    public function createCheckin($request)
    {
        $check = $this->mCheckin->where('associate_id', $request->id)
            ->whereDate('checkin', Carbon::parse($request->checkin)->format('Y-m-d'))
            ->first();
        if (!empty($check)) {
            return null;
        }
        return $this->mCheckin->create([
            'created_at' => Carbon::parse($request->checkin)->format('Y-m-d').' 00:00:00',
            'associate_id' => $request->id,
            'status' => true,
            'user_id' => Auth::user()->id,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
        ]);
    }

    public function updateCheckin($id, $request)
    {
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return [
                'status'    => false,
                'message'   => 'Contraseña incorrecta'
            ];
        }
        $checkin = $this->mCheckin->find($id);
        if ($checkin) {
            $checkin->update($request->all());
            $checkin->update(['user_id', Auth::user()->id]);
            return $checkin;
        }
    }
}
