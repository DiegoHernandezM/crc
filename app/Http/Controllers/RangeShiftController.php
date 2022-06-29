<?php

namespace App\Http\Controllers;

use App\Models\RangeShift;
use Illuminate\Http\Request as RequestHttp;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class RangeShiftController extends Controller
{
    protected $mRangeShift;

    public function __construct()
    {
        $this->mRangeShift = new RangeShift();
    }

    public function createRange(RequestHttp $oRequest)
    {
        try {
            $range = $this->mRangeShift->create([
               'day'  => $oRequest->day,
               'area_id' => Auth::user()->area_id ?? null
            ]);
            return Redirect::route('teams')->with('success', 'Fecha de cambio de horario asignada');
        } catch (\Exception $e) {
            return Redirect::route('teams')->with('error', $e->getMessage());
        }
    }
}
