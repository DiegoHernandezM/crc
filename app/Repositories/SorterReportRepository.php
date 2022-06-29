<?php

namespace App\Repositories;

use App\Models\Area;
use App\Models\Associate;
use App\Models\Board;
use App\Models\BonusStaffManager;
use App\Models\Checkin;
use App\Models\ProductivitySorter;
use App\Models\SorterBonus;
use Carbon\Carbon;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SorterReportRepository
{
    protected $mProductivitySorter;
    protected $mCheckin;
    protected $mBoard;
    protected $mSorterBonus;
    protected $mAssociates;
    protected $mSorterStaffBonus;

    public function __construct()
    {
        $this->mProductivitySorter= new ProductivitySorter();
        $this->mCheckin = new Checkin();
        $this->mBoard = new Board();
        $this->mSorterBonus = new SorterBonus();
        $this->mAssociates = new Associate();
        $this->mSorterStaffBonus = new BonusStaffManager();
    }

    public function calculateSorterBonus($startDay, $endDay)
    {
        $productivity = $this->mProductivitySorter->whereBetween('date', [$startDay, $endDay])->orderBy('associate_id')->orderBy('first_induction', 'asc')->get();

        foreach ($productivity as $key => $value) {
            $check = $this->mCheckin->where('associate_id', $value->associate_id)
                ->where('checkout', '>=', $value->first_induction)
                ->where('checkin', '<=', $value->last_induction)
                ->first();
            if (empty($check)) {
                $newCheck = new Checkin;
                $newCheck->checkin = $value->first_induction;
                $time1 = Carbon::parse($value->first_induction);
                for ($i=1; $i <= 5; $i++) {
                    if (isset($productivity[$key+$i])) {
                        if ($productivity[$key+$i]->associate_id == $value->associate_id) {
                            $time2 = Carbon::parse($productivity[$key+$i]->first_induction);
                            if ($time1->diffInHours($time2) > 12 || $productivity[$key+($i-1)]->last_induction > $productivity[$key+$i]->last_induction) {
                                $newCheck->checkout = $productivity[$key+($i-1)]->last_induction;
                                break;
                            }
                        } else {
                            $newCheck->checkout = $productivity[$key+($i-1)]->last_induction;
                            break;
                        }
                    } else {
                        $newCheck->checkout = $productivity[$key+($i-1)]->last_induction;
                        break;
                    }
                }
                $newCheck->associate_id = $value->associate_id;
                $newCheck->status = 1;
                $newCheck->user_id = 1;
                $newCheck->save();
            }
        }

        $checkin = $this->mCheckin->whereBetween('checkin', [
            $startDay.' 00:00:00',
            $endDay.' 23:59:59'
        ])->get();

        $bonusBoard = $this->mBoard->where('area_id', Area::SORTER)
            ->where('subarea_id', DB::table('subareas')->select('id')->where('name', 'Induccion')->where('area_id', Area::SORTER)->value('id'))
            ->get();

        foreach ($checkin as $key => $ch) {
            $bonusQuery = $this->mProductivitySorter->select('associate_id', DB::raw('sum(ppk) as ppk'), DB::raw('sum(total_time) as time'), DB::raw('date(min(first_induction)) as bonus_date'))
                ->where([['associate_id', '=', $ch->associate_id],['first_induction', '>=', $ch->checkin],['last_induction', '<=', $ch->checkout]])
                ->groupBy('associate_id')
                ->first();

            $exists = $this->mSorterBonus->where([
                ['associate_id', '=', $ch->associate_id],
                ['bonus_date', '=', $bonusQuery->bonus_date]
            ])->first();
            if (!empty($bonusQuery) && empty($exists)) {
                $divisor = ($bonusQuery->time * 60 > 0 ) ? $bonusQuery->time * 60 : 1;
                $ppk = ((int)$bonusQuery->ppk > 0 ) ? (int)$bonusQuery->ppk / $divisor : 0;

                $bonusAmount = $bonusBoard->where('quantity', '<=', $ppk)->sortByDesc('quantity')->first();
                $this->mSorterBonus->create([
                    'associate_id' => $ch->associate_id,
                    'bonus_date' => $bonusQuery->bonus_date,
                    'ppk_shift' => $ppk,
                    'bonus_amount' => $bonusAmount->bono ?? 0
                ]);
            }
        }
        return 'ok';
    }

    public function runReportBonus($init, $end, $save = false)
    {
        try {
            setlocale(LC_ALL, 'es_ES');

            $initDate = ($init == null) ? Carbon::parse()->subDays(7)->format('Y-m-d') : Carbon::parse($init)->format('Y-m-d');
            $endDate = ($end == null) ? Carbon::parse()->subDays(1)->format('Y-m-d') : Carbon::parse($end)->format('Y-m-d');

            $waves = $this->mProductivitySorter->whereBetween('date', [$initDate, $endDate])->orderBy('date')->distinct()->select('date', 'wave')->get();
            $associates = $this->mAssociates->where('area_id', Area::SORTER)->with(['productivitySorter' => function ($q) use ($initDate, $endDate) {
                    $q->whereBetween('productivity_sorter.date', [$initDate, $endDate])->orderBy('productivity_sorter.date');
                    $q->orderBy('date');
                }])
                ->with(['bonusSorter' => function ($q) use ($initDate, $endDate) {
                    $q->whereBetween('sorter_bonus.bonus_date', [$initDate, $endDate])->orderBy('sorter_bonus.bonus_date');
                    $q->orderBy('bonus_date');
                }])
                ->get()->toArray();

            foreach ($waves as $k => $wave) {
                $sortWave[$wave->date][] = $wave->wave;
            }

            $styleArray = [
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ];

            $spreadsheet = new Spreadsheet();

            // CONSOLIDADO POR OLA

            $spreadsheet->getDefaultStyle()->getFont()->setSize(14);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('CONSOLIDADO POR OLAS');
            $sheet->getStyle('A1:BW205')->applyFromArray($styleArray);
            $sheet->setCellValue('A2', 'ASOCIADO');
            $sheet->mergeCells('A2:C2');
            $sheet->setCellValue('A3', 'NO. EMPLEADO');
            $sheet->setCellValue('B3', 'NOMBRE');
            $sheet->setCellValue('C3', 'USUARIO');

            $merge = 4;
            $col = 4;
            $sumRow = 4;
            $totals = [];
            $pieces = 0;


            foreach ($sortWave as $k => $date) {
                $sheet->setCellValue($this->getCol($merge) . '1', $k);
                if (count($date) > 1) {
                    $sheet->mergeCells($this->getCol($merge). '1:'.$this->getCol((count($date) > 1) ? ($merge + (count($date) * 3)) -1 : $merge + 2 ).'1');
                } else {
                    $sheet->mergeCells($this->getCol($merge). '1:'.$this->getCol($merge + 2 ).'1');
                }
                $merge = (count($date) > 1) ? ($merge + (count($date) * 3))  : $merge + (count($date) * 3);
                foreach ($date as $wave) {
                    $sheet->setCellValue($this->getCol($col) . '2', $wave);
                    $sheet->setCellValue($this->getCol($col) . '3', 'TIEMPO');
                    $sheet->setCellValue($this->getCol($col+1) . '3', 'PPK');
                    $sheet->setCellValue($this->getCol($col+2) . '3', 'PZAS');
                    $sheet->mergeCells($this->getCol($col). '2:'.$this->getCol($col+2).'2');
                    $row = 4;
                    foreach ($associates as $k => $associate) {
                        if (count($associate['productivity_sorter']) > 0) {
                            $sheet->setCellValue('A'.$row , $associate['employee_number']);
                            $sheet->setCellValue('B'.$row , $associate['name']);
                            $sheet->setCellValue('C'.$row , $associate['wamas_user']);
                            foreach ($associate['productivity_sorter'] as $prod) {
                                if ($wave === $prod['wave']) {
                                    $sheet->setCellValue($this->getCol($col).$row , $prod['total_time'] ?? '--');
                                    $sheet->setCellValue($this->getCol($col + 1).$row , $prod['ppk'] ?? '--');
                                    $sheet->setCellValue($this->getCol($col + 2).$row , $prod['pieces'] ?? '--');
                                    $totals[$col + 2][] =  $pieces + $prod['pieces'] ;

                                }
                            }
                            $row++;
                        }
                    }
                    $sumRow =  $row;
                    $col = $col + 3;
                }
            }

            foreach ($totals as $k => $total) {
                $sheet->setCellValue($this->getCol($k).$sumRow , array_sum($total));
            }

            $sheet->setCellValue('A'.$sumRow , 'TOTAL');
            $sheet->mergeCells('A'.$sumRow.':C'.$sumRow);

            $col = 4;
            $color = 'D9D9D9D9';
            for ($i = 0; $i <= (count($waves) -1); $i++) {
                if ($color == 'D9D9D9D9') {
                    $sheet->getStyle($this->getCol($col)."4:".$this->getCol($col+2).($sumRow - 1))
                        ->getFill()
                        ->setFillType('solid')
                        ->getStartColor()
                        ->setARGB($color);
                    $color = 'FFFFFFFF';
                } else {
                    $color = 'D9D9D9D9';
                }
                $col = $col + 3;
            }

            $sheet->getStyle('A1:'.$this->getCol($col-1).($sumRow))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle('thin');
            $sheet->freezePane('D4');

            foreach (range('A', 'CHD') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }


            // CONSOLIDADO POR DIA

            $productivity = $this->mSorterBonus->whereBetween('sorter_bonus.bonus_date', [$initDate, $endDate])->orderBy('sorter_bonus.bonus_date')->get()->toArray();
            $sortProd = [];
            $datesProd = [];
            foreach ($productivity as $k => $prod) {
                $sortProd[$prod['bonus_date']][] = $prod;
                $datesProd[] = $prod['bonus_date'];
            }

            $datesProd = array_values(array_unique($datesProd));

            $prodSheet =  $spreadsheet->createSheet();
            $prodSheet->setTitle('CONSOLIDADO POR DIA');
            $prodSheet->getStyle('A1:BW205')->applyFromArray($styleArray);
            $prodSheet->setCellValue('A2', 'ASOCIADO');
            $prodSheet->mergeCells('A2:C2');
            $prodSheet->setCellValue('A3', 'NO. EMPLEADO');
            $prodSheet->setCellValue('B3', 'NOMBRE');
            $prodSheet->setCellValue('C3', 'USUARIO');

            $col = 4;

            foreach ($datesProd as $key => $date)
            {
                $prodSheet->setCellValue($this->getCol($col) . '2', $date);
                $prodSheet->setCellValue($this->getCol($col) . '3', 'PPK');
                $prodSheet->setCellValue($this->getCol($col+1) . '3', 'PZAS');
                $prodSheet->setCellValue($this->getCol($col+2) . '3', 'MONTO');
                $prodSheet->mergeCells($this->getCol($col). '2:'.$this->getCol($col+2).'2');

                $row = 4;
                foreach ($associates as $k => $associate) {
                    if (count($associate['bonus_sorter']) > 0) {
                        $prodSheet->setCellValue('A'.$row , $associate['employee_number']);
                        $prodSheet->setCellValue('B'.$row , $associate['name']);
                        $prodSheet->setCellValue('C'.$row , $associate['wamas_user']);
                        foreach ($associate['bonus_sorter'] as $prod) {
                            if ($date === $prod['bonus_date']) {
                                $pieces  = array_sum(array_column($associate['productivity_sorter'],'pieces'));
                                $prodSheet->setCellValue($this->getCol($col).$row , $prod['ppk_shift'] ?? '--');
                                $prodSheet->setCellValue($this->getCol($col + 1).$row , $pieces ?? '--');
                                $prodSheet->setCellValue($this->getCol($col + 2).$row , '$ '.$prod['bonus_amount'] ?? '--');
                            }
                        }
                        $row++;
                    }
                }
                $sumRow =  $row;
                $col = $col + 3;
            }

            foreach (range('A', 'CHD') as $columnID) {
                $prodSheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            $prodSheet->freezePane('D4');
            $prodSheet->getStyle('A1:'.$this->getCol($col-1).($sumRow - 1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle('thin');
            $col = 4;
            $color = 'D9D9D9D9';
            for ($i = 0; $i <= (count($datesProd) -1); $i++) {
                if ($color == 'D9D9D9D9') {
                    $prodSheet->getStyle($this->getCol($col)."4:".$this->getCol($col+2).($sumRow - 1))
                        ->getFill()
                        ->setFillType('solid')
                        ->getStartColor()
                        ->setARGB($color);
                    $color = 'FFFFFFFF';
                } else {
                    $color = 'D9D9D9D9';
                }
                $col = $col + 3;
            }

            // TABLA DE BONO

            $bonusSheet =  $spreadsheet->createSheet();
            $bonusSheet->setTitle('TABLA DE BONOS');
            $bonusSheet->getStyle('A1:BW205')->applyFromArray($styleArray);
            $bonusSheet->setCellValue('A2', 'ASOCIADO');
            $bonusSheet->mergeCells('A2:C2');
            $bonusSheet->setCellValue('A3', 'NO. EMPLEADO');
            $bonusSheet->setCellValue('B3', 'NOMBRE');
            $bonusSheet->setCellValue('C3', 'USUARIO');

            $col = 4;
            foreach ($datesProd as $key => $date)
            {
                $bonusSheet->setCellValue($this->getCol($col) . '2', $date);
                $row = 4;
                foreach ($associates as $k => $associate) {
                    if (count($associate['bonus_sorter']) > 0) {
                        $bonusSheet->setCellValue('A'.$row , $associate['employee_number']);
                        $bonusSheet->setCellValue('B'.$row , $associate['name']);
                        $bonusSheet->setCellValue('C'.$row , $associate['wamas_user']);
                        foreach ($associate['bonus_sorter'] as $prod) {
                            if ($date === $prod['bonus_date']) {
                                $bonusSheet->setCellValue($this->getCol($col ).$row , '$ '.$prod['bonus_amount'] ?? '--');
                            }
                        }
                        $row++;
                    }
                }
                $sumRow =  $row;
                $col++;
            }

            $bonusSheet->freezePane('D4');
            $bonusSheet->getStyle('A1:'.$this->getCol($col-1).($sumRow - 1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle('thin');

            foreach (range('A', 'CHD') as $columnID) {
                $bonusSheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $col = 4;
            $color = 'D9D9D9D9';
            for ($i = 0; $i <= (count($datesProd) -1); $i++) {
                if ($color == 'D9D9D9D9') {
                    $bonusSheet->getStyle($this->getCol($col)."4:".$this->getCol($col).($sumRow - 1))
                        ->getFill()
                        ->setFillType('solid')
                        ->getStartColor()
                        ->setARGB($color);
                    $color = 'FFFFFFFF';
                } else {
                    $color = 'D9D9D9D9';
                }
                $col++;
            }

            $response = response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });
            if ($save === false) {
                $response->setStatusCode(200);
                $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $response->headers->set('Content-Disposition', 'attachment; filename="your_file.xlsx"');
                $response->headers->set('Access-Control-Allow-Origin', '*');
                return $response->send();
            } else {
                $fileName = uniqid();
                $writer = new Xlsx($spreadsheet);
                $writer->save(public_path('files/'.$fileName.'.xlsx'));
                return $fileName;
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function runReportBonusStaff($init, $end, $save = false)
    {
        try {
            setlocale(LC_ALL, 'es_ES');
            $initDate = ($init == null) ? Carbon::now()->subDays(7)->format('Y-m-d') : Carbon::parse($init)->format('Y-m-d');
            $endDate = ($end == null) ? Carbon::now()->subDays(1)->format('Y-m-d') : Carbon::parse($end)->format('Y-m-d');
            $countDays = Carbon::parse($initDate)->diffInDays(Carbon::parse($endDate));

            $staff = $this->mAssociates->join('areas', 'areas.id', '=', 'associates.area_id')
                ->join('subareas', 'subareas.id', '=', 'associates.subarea_id')
                ->with(['checkin' => function ($q) use ($initDate, $endDate) {
                    $q->whereBetween('checkin', [$initDate, $endDate]);
                }])
                ->where('associates.elegible', 1)
                ->where('associates.area_id', Area::SORTER)
                ->where('associates.subarea_id', DB::table('subareas')->select('id')->where('name', 'Staff')->where('area_id', Area::SORTER)->value('id'))
                ->select('associates.*', 'areas.name as area', 'subareas.name as subarea')
                ->get();


            $managers = $this->mAssociates->join('areas', 'areas.id', '=', 'associates.area_id')
                ->join('subareas', 'subareas.id', '=', 'associates.subarea_id')
                ->where('associates.elegible', 1)
                ->where('associates.area_id', Area::SORTER)
                ->where('associates.subarea_id', DB::table('subareas')->select('id')->where('name', 'Encargado')->where('area_id', Area::SORTER)->value('id'))
                ->select('associates.*', 'areas.name as area', 'subareas.name as subarea')
                ->get();

            $spreadsheet = new Spreadsheet();

            // BONO STAFF

            $styleArray = [
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ];

            $spreadsheet->getDefaultStyle()->getFont()->setSize(14);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('BONOS DE STAFF');
            $sheet->getStyle('A1:BW205')->applyFromArray($styleArray);
            $sheet->setCellValue('A2', 'NO. DE EMPLEADO');
            $sheet->setCellValue('B2', 'NOMBRE');
            $sheet->setCellValue('C2', 'FECHA INGRESO');
            $sheet->setCellValue('D2', 'BONO');
            $sheet->setCellValue('E2', 'FALTAS');

            $rows = 3;

            foreach ($staff as $k => $associate) {
                $amount = $this->mBoard->where('area_id', $associate->area_id)
                    ->where('subarea_id', DB::table('subareas')->select('id')
                        ->where('name', 'Staff')->where('area_id', Area::SORTER)->value('id'))->first();
                $absences = $countDays - count($associate->checkin);
                $sheet->setCellValue('A' . $rows, $associate->employee_number);
                $sheet->setCellValue('B' . $rows, $associate->name);
                $sheet->setCellValue('C' . $rows, Carbon::parse($associate->entry_date)->format('Y-m-d'));
                $sheet->setCellValue('D' . $rows, '$ '.$amount->bono);
                $sheet->setCellValue('E' . $rows, ($absences != 0) ? $absences : 0);

                $rows++;
            }

            $sheet->getStyle('A2:E'.($rows -1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle('thin');

            foreach (range('A', 'CHD') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // BONO MANAGERS

            $sheetManagers =  $spreadsheet->createSheet();
            $sheetManagers->setTitle('BONOS MANAGERS');
            $sheetManagers->getStyle('A1:BW205')->applyFromArray($styleArray);
            $sheetManagers->setCellValue('A2', 'NO. DE EMPLEADO');
            $sheetManagers->setCellValue('B2', 'NOMBRE');
            $sheetManagers->setCellValue('C2', 'FECHA INGRESO');
            $sheetManagers->setCellValue('D2', 'BONO');
            $sheetManagers->setCellValue('E2', 'FALTAS');

            foreach ($managers as $k => $associate) {
                $amount = $this->mBoard->where('area_id', $associate->area_id)
                    ->where('subarea_id', DB::table('subareas')->select('id')
                        ->where('name', 'Encargado')->where('area_id', Area::SORTER)->value('id'))->first();
                $absences = $countDays - count($associate->checkin);
                $sheetManagers->setCellValue('A' . $rows, $associate->employee_number);
                $sheetManagers->setCellValue('B' . $rows, $associate->name);
                $sheetManagers->setCellValue('C' . $rows, Carbon::parse($associate->entry_date)->format('Y-m-d'));
                $sheetManagers->setCellValue('D' . $rows, '$ '.$amount->bono);
                $sheetManagers->setCellValue('E' . $rows, ($absences != 0) ? $absences : 0);

                $rows++;
            }
            $sheetManagers->getStyle('A2:E'.($rows -1) )
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle('thin');

            foreach (range('A', 'CHD') as $columnID) {
                $sheetManagers->getColumnDimension($columnID)->setAutoSize(true);
            }

            $response = response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            if ($save === false) {
                $response->setStatusCode(200);
                $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $response->headers->set('Content-Disposition', 'attachment; filename="your_file.xlsx"');
                $response->headers->set('Access-Control-Allow-Origin', '*');
                return $response->send();
            } else {
                $fileName = uniqid();
                $writer = new Xlsx($spreadsheet);
                $writer->save(public_path('files/'.$fileName.'.xlsx'));
                return $fileName;
            }

        } catch (\Exception $e) {
            return $e;
        }
    }

    public function processBonusStaff($week)
    {
        try {
            $bonus = $this->mSorterStaffBonus->where('year_week', $week)->get();
            if (count($bonus) === 0) {
                $associates = $this->mAssociates->where(function ($q) {
                    return $q
                        ->where('area_id', Area::SORTER)
                        ->whereIn('subarea_id', [
                            DB::table('subareas')->select('id')->where('name', 'Staff')->where('area_id', Area::SORTER)->value('id'),
                            DB::table('subareas')->select('id')->where('name', 'Encargado')->where('area_id', Area::SORTER)->value('id')
                        ]);
                })->get();

                foreach ($associates as $k => $associate) {
                    $amount = $this->mBoard->where('area_id', $associate->area_id)
                        ->where('subarea_id', $associate->subarea_id )->first();
                    $this->mSorterStaffBonus->create([
                        'associate_id' => $associate->id,
                        'area_id' => $associate->area_id,
                        'subarea_id' => $associate->subarea_id,
                        'year_week' => $week,
                        'bonus_amount' => $amount->bono
                    ]);
                }
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getPendingProductivity()
    {
        try {
            $datesBonus = $this->mSorterBonus->select('bonus_date')->distinct()->orderBy('bonus_date')->get()->toArray();
            $dates = [];
            foreach ($datesBonus as $k => $value) {
                $dates[] = $value['bonus_date'];
            }
            $productivity = $this->mProductivitySorter->whereNotIn('date', $dates)->orderBy('date')->get();
            $sortData = [];

            foreach($productivity->unique('wave') as $prod){
                $sortData[$prod['date']][] = $prod['wave'];
            }

            $data = [];
            foreach ($sortData as $k => $value) {
                $data[] = [
                    'day' => $k,
                    'wave' => join(", ",$value),
                    'count' => count($value)
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function getCol($num)
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($num);
    }
}