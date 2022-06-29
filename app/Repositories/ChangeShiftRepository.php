<?php
namespace App\Repositories;

use App\Models\Area;
use App\Models\Associate;
use App\Models\RangeShift;
use App\Models\Log as Logger;
use App\Models\Shift;
use Carbon\Carbon;

class ChangeShiftRepository
{
    protected $mRangeShift;
    protected $mAssociates;
    protected $mShift;

    public function __construct()
    {
        $this->mRangeShift = new RangeShift();
        $this->mAssociates = new Associate();
        $this->mShift = new Shift();
    }

    public function changeShift()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $change = $this->mRangeShift->where('day', $today)->where('area_id', Area::SORTER)->first();
            if ($change !== null) {
                $morning = $this->getShift('MATUTINO');
                $evening = $this->getShift('VESPERTINO');
                $night = $this->getShift('NOCTURNO');
                $data = [
                    $morning,
                    $evening,
                    $night
                ];
                $this->updateShift($data);
            }
            return true;
        } catch (\Exception $e) {
            $logData = [
                'message'       => $e->getMessage(),
                'loggable_id'   => Logger::LOG_SYSTEM,
                'loggable_type' => 'Sistem',
                'user_id'       => 1,
            ];
            $log = new Logger();
            $log->create($logData);
        }
    }

    private function getShift($nameShift)
    {
        $next = [
            'MATUTINO' => 'VESPERTINO',
            'VESPERTINO' => 'NOCTURNO',
            'NOCTURNO' => 'MATUTINO'
        ];
        $aIds = [];
        $shift = $this->mShift->where('name', $nameShift)->first();
        $associates = $this->mAssociates->where('shift_id', $shift->id)->select('id')->get();
        $nextShift = $this->mShift->where('name', $next[$nameShift])->first();
        foreach ($associates as $associate) {
            $aIds[] = $associate->id;
        }
        return [
            'ids' => $aIds,
            'next' => $nextShift->id
        ];
    }

    private function updateShift($data)
    {
        foreach ($data as $value) {
            $this->mAssociates->whereIn('id', $value['ids'])
                ->update(['shift_id' => $value['next']]);
        }
    }
}
