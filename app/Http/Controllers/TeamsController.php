<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssociateCollection;
use App\Models\Area;
use App\Models\Associate;
use App\Models\RangeShift;
use App\Models\Subarea;
use App\Models\Shift;
use function Clue\StreamFilter\fun;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as RequestHttp;

class TeamsController extends Controller
{
    protected $mAssociate;
    protected $mAreas;
    protected $mSubarea;
    protected $mRangeShift;

    public function __construct()
    {
        $this->mAssociate = new Associate();
        $this->mAreas = new Area();
        $this->mSubarea = new Subarea();
        $this->mRangeShift = new RangeShift();
    }

    public function index()
    {
        $this->authorize('Productividad.Plantilla');

        $associates = $this->getAssociates();
        $shifts = Shift::where(function ($q) {
            if (Auth::user()->area_id != null) {
                $q->where('area_id', Auth::user()->area_id);
            }
            return $q;
        })->get();
        return Inertia::render('Teams/Index', [
            'filters' => Request::all('search', 'trashed'),
            'associates' => $associates,
            'shifts' => $shifts,
            'range' => $this->mRangeShift->orderBy('created_at', 'desc')->first()
        ]);
    }

    public function edit($withView = true)
    {
        $this->authorize('Productividad.Plantilla');
        $aMembers = [];
        $members = $this->getAssociates();

        $subareas = $this->mSubarea->where(function ($q) {
            if (Auth::user()->area_id !== null) {
                return $q->where('subareas.area_id', Auth::user()->area_id);
            }
        })->get()->toArray();

        foreach ($subareas as $subarea) {
            $aMembers[$subarea['id']] = $this->associatesSubarea($members, $subarea['id']);
        }
        if ($withView === true) {
            $view = (Auth::user()->area_id === Area::PICKING) ? 'Picking/Index' : 'Sorter/Index';
            return Inertia::render('Teams/'.$view, [
                'filters' => Request::all('search', 'trashed'),
                'members' => $members,
                'membersSubarea' => $aMembers,
                'subareas' => $subareas
            ]);
        } else {
            return ['members' => $aMembers, 'subareas' => $subareas];
        }
    }

    private function associatesSubarea($associates, $subarea)
    {
        $this->authorize('Productividad.Plantilla');
        $members = [];
        foreach ($associates as $associate) {
            if ($subarea === $associate->subarea_id) {
                $members[] = $associate;
            }
        }
        return $members;
    }

    private function getAssociates()
    {
        $this->authorize('Productividad.Plantilla');
        $associates = $this->mAssociate->join('areas', 'areas.id', '=', 'associates.area_id')
            ->join('subareas', 'subareas.id', '=', 'associates.subarea_id')
            ->join('associate_types', 'associate_types.id', '=', 'associates.associate_type_id')
            ->join('shifts', 'shifts.id', '=', 'associates.shift_id')
            ->where(function ($q) {
                if (Auth::user()->area_id !== null) {
                    return $q->where('associates.area_id', Auth::user()->area_id);
                }
            })
            ->select(
                'associates.*',
                'associate_types.name as associate_type',
                'subareas.name as subarea',
                'shifts.name as shift'
            )->get();

        return $associates;
    }

    public function getAllData()
    {
        $this->authorize('Productividad.Plantilla');
        return response()->json($this->edit(false));
    }
}
