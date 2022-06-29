<?php

namespace App\Repositories;

use App\Log;
use App\Models\Area;
use App\Models\Associate;
use App\Models\Board;
use App\Models\Checkin;
use App\Models\PickingBonus;
use App\Models\PickingProductivity;
use App\Models\ProductivitySorter;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Redis;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportRepository
{
    protected $mAssociate;
    protected $mCheckin;
    protected $mProductivitySorter;

    public function __construct()
    {
        $this->mAssociate = new Associate();
        $this->mCheckin = new Checkin();
        $this->mProductivitySorter = new ProductivitySorter();
    }

    public function getHistoricAssociate($associate)
    {
        setlocale(LC_ALL, 'es_ES');
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
            ],
        ];
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setSize(14);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Asistencias');
        $sheet->getStyle('B2:B6')
            ->applyFromArray($styleArray)
            ->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('D9D9D9D9');


        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setCellValue('B2', 'NO. EMPLEADO');
        $sheet->setCellValue('B3', 'NOMBRE');
        $sheet->setCellValue('B4', 'AREA');
        $sheet->setCellValue('B5', 'SUBAREA');
        $sheet->setCellValue('B6', 'HORARIO ASIGNADO');

        $sheet->setCellValue('C2', $associate[0]->employee_number);
        $sheet->setCellValue('C3', $associate[0]->name);
        $sheet->setCellValue('C4', $associate[0]->area->name);
        $sheet->setCellValue('C5', $associate[0]->subarea->name);
        $sheet->setCellValue('C6', $associate[0]->shift->name);

        $sheet->getStyle('B2:C6')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        $sheet->setCellValue('B9', 'DIA');
        $sheet->setCellValue('C9', 'ENTRADA');
        $sheet->setCellValue('D9', 'SALIDA');
        $sheet->setCellValue('E9', 'HORAS REGISTRADAS');
        $sheet->setCellValue('F9', 'HORAS ASIGNADAS');
        $sheet->setCellValue('G9', 'HORAS EXTRA');

        $sheet->getStyle('B9:G9')
            ->applyFromArray($styleArray)
            ->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('D9D9D9D9');

        $rows = 10;

        if (count($associate[0]->checkin) > 0) {
            foreach ($associate[0]->checkin as $register) {
                $sheet->setCellValue('B' . $rows, Carbon::parse($register->checkin)->format('Y-m-d'));
                $sheet->setCellValue('C' . $rows, Carbon::parse($register->checkin)->format('H:i:s'));
                $sheet->setCellValue('D' . $rows, $register->checkout !== null
                    ? Carbon::parse($register->checkout)->format('H:i:s')
                    : '--');
                $sheet->setCellValue('E' . $rows, $register->hours);
                foreach ( $associate[0]->shift->shifts as $shift) {
                    if ($register->checkin !== null && $register->checkout !== null) {
                        if ($shift->day === Carbon::parse($register->checkin)->dayOfWeek) {
                            $sheet->setCellValue('F' . $rows, $shift->assign);
                            $sheet->setCellValue('G' . $rows,
                                $this->getHours($associate[0]->unionized, $register->hours, $shift->assign));
                        }
                    }
                }
                $rows++;
            }
        }

        $sheet->getStyle('B9:G' . ($rows - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="your_file.xlsx"');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response->send();
    }

    public function getHistoricGeneral($registers)
    {
        setlocale(LC_ALL, 'es_ES');
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setSize(14);
        $sheet = $spreadsheet->getActiveSheet()->freezePane('A2');
        $sheet->setTitle('Asistencias');
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
            ],
        ];
        $sheet->getStyle('A1:I1')
            ->applyFromArray($styleArray)
            ->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('D9D9D9D9');

        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->setCellValue('A1', 'DIA');
        $sheet->setCellValue('B1', 'ENTRADA');
        $sheet->setCellValue('C1', 'SALIDA');
        $sheet->setCellValue('D1', 'HORAS REGISTRADAS');
        $sheet->setCellValue('E1', 'HORAS ASIGNADAS');
        $sheet->setCellValue('F1', 'HORAS EXTRA');
        $sheet->setCellValue('G1', 'TURNO');
        $sheet->setCellValue('H1', 'NO. EMPLEADO');
        $sheet->setCellValue('I1', 'NOMBRE');

        $rows = 2;

        foreach ($registers as $register) {
            foreach ( $register->associate->shift->shifts as $shift) {
                if ($register->checkin !== null && $register->checkout !== null) {
                    if ($shift->day === Carbon::parse($register->checkin)->dayOfWeek) {
                        $sheet->setCellValue('E' . $rows, $shift->assign);
                        $sheet->setCellValue('F' . $rows,
                            $this->getHours($register->associate->unionized, $register->hours, $shift->assign));
                    }
                }
            }
            $sheet->setCellValue('A' . $rows, Carbon::parse($register->checkin)->format('Y-m-d'));
            $sheet->setCellValue('B' . $rows, Carbon::parse($register->checkin)->format('H:i:s'));
            $sheet->setCellValue('C' . $rows, $register->checkout ? Carbon::parse($register->checkout)->format('H:i:s') : '');
            $sheet->setCellValue('D' . $rows, $register->hours);
            $sheet->setCellValue('G' . $rows, $register->associate->shift->name);
            $sheet->setCellValue('H' . $rows, $register->associate->employee_number);
            $sheet->setCellValue('I' . $rows, $register->associate->name);
            $rows++;
        }

        $sheet->getStyle('A1:I' . ($rows - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        foreach (range('A', 'I') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="your_file.xlsx"');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response->send();
    }

    public function runReportHours($init = null, $end = null, $area = null, $save = false)
    {
        try {
            $byArea = ($area != null) ? $area : Area::PICKING;
            $locations = [];
            $today = Carbon::parse("last Wednesday");
            $startDay = ($init != null) ? $init : Carbon::parse($today)->subDays(7)->format('Y-m-d');
            $endDay = ($end != null) ? $end : Carbon::parse($today)->subDays(1)->format('Y-m-d');
            $countDays = ($init != null) ? Carbon::parse($startDay)->diffInDays(Carbon::parse($endDay)) : 0;

            $associates = Associate::join('areas', 'areas.id', '=', 'associates.area_id')
                ->join('associate_types', 'associate_types.id', '=', 'associates.associate_type_id')
                ->join('shifts', 'shifts.id', '=', 'associates.shift_id')
                ->where('associates.area_id', $byArea)
                ->with(['checkin' => function ($q) use ($startDay, $endDay) {
                    $q->join('associates', 'associates.id', '=', 'checkin.associate_id');
                    $q->whereBetween('checkin', [$startDay, $endDay]);
                    $q->select(DB::raw('TIMESTAMPDIFF(MINUTE, checkin.checkin, checkin.checkout) / 60 as register_hours'), 'checkin.*', 'associates.name', 'associates.employee_number');
                    $q->orderBy('checkin.checkin', 'asc');
                }])
                ->select(
                    'associates.*',
                    'associate_types.name as associate_type',
                    DB::raw('TIMESTAMPDIFF(HOUR, shifts.checkin, shifts.checkout) as assign_hours')
                )->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'NO. EMPLEADO');
            $sheet->setCellValue('B1', 'NOMBRE');
            $sheet->setCellValue('C1', 'AREA');

            if ($countDays == 0) {
                $sheet->setCellValue('D1', Carbon::parse($today)->subDays(7)->format('Y-m-d'));
                $sheet->setCellValue('E1', Carbon::parse($today)->subDays(6)->format('Y-m-d'));
                $sheet->setCellValue('F1', Carbon::parse($today)->subDays(5)->format('Y-m-d'));
                $sheet->setCellValue('G1', Carbon::parse($today)->subDays(4)->format('Y-m-d'));
                $sheet->setCellValue('H1', Carbon::parse($today)->subDays(3)->format('Y-m-d'));
                $sheet->setCellValue('I1', Carbon::parse($today)->subDays(2)->format('Y-m-d'));
                $sheet->setCellValue('J1', Carbon::parse($today)->subDays(1)->format('Y-m-d'));
            } else {
                $col = 'D';
                for ($i = 0; $i <= $countDays; $i++) {
                    $sheet->setCellValue($col . '1', Carbon::parse($startDay)->addDays($i)->format('Y-m-d'));
                    $locations[Carbon::parse($startDay)->addDays($i)->dayOfWeek] = ['cell' => $col];
                    $col++;
                }
            }

            $rows = 2;
            $registers = count($associates) + 1;
            $checks = [];

            foreach ($associates as $associate) {
                $sheet->setCellValue('A' . $rows, $associate->employee_number);
                $sheet->setCellValue('B' . $rows, $associate->name);
                $sheet->setCellValue('C' . $rows, $associate->associate_type);

                if (count($associate->checkin) > 0) {
                    foreach ($associate->checkin as $checkin) {
                        $checks[] = $checkin;
                        $content = $this->setCheckinCell($checkin, $associate->assign_hours, $associate->unionized, $locations);
                        $sheet->setCellValue($content['cell'] . $rows, $content['value']);
                    }
                }
                $rows++;
            }
            if (count($locations) > 0) {
                $last = end($locations);
                $spreadsheet->getActiveSheet()->setAutoFilter("A1:" . $last['cell'] . '' . $registers);
            } else {
                $spreadsheet->getActiveSheet()->setAutoFilter("A1:J" . $registers);
            }

            $spreadsheet->getActiveSheet()->setTitle('Horas extra');

            $worksheet1 =  $spreadsheet->createSheet();
            $worksheet1->setTitle('Asistencias');

            $worksheet1->setCellValue('A1', 'DIA');
            $worksheet1->setCellValue('B1', 'ENTRADA');
            $worksheet1->setCellValue('C1', 'SALIDA');
            $worksheet1->setCellValue('D1', 'NO. EMPLEADO');
            $worksheet1->setCellValue('E1', 'NOMBRE');

            $rowsW = 2;

            foreach ($checks as $key => $register) {
                $worksheet1->setCellValue('A' . $rowsW, Carbon::parse($register->checkin)->format('Y-m-d'));
                $worksheet1->setCellValue('B' . $rowsW, Carbon::parse($register->checkin)->format('H:i:s'));
                $worksheet1->setCellValue('C' . $rowsW, Carbon::parse($register->checkout)->format('H:i:s'));
                $worksheet1->setCellValue('D' . $rowsW, $register->employee_number);
                $worksheet1->setCellValue('E' . $rowsW, $register->name);
                $rowsW++;
            }

            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                $spreadsheet->setActiveSheetIndex($spreadsheet->getIndex($worksheet));

                $sheet = $spreadsheet->getActiveSheet();
                $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);

                foreach ($cellIterator as $cell) {
                    $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                }
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
                $fileNmae = uniqid();
                $writer = new Xlsx($spreadsheet);
                $writer->save(public_path('files/' . $fileNmae . '.xlsx'));
                return $fileNmae;
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function getDataFromExcel($file)
    {
        $nameFile = $file->getClientOriginalName();
        $arrFile = explode('.', $nameFile);
        $extension = end($arrFile);

        if ($extension === 'csv') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $encoding = \PhpOffice\PhpSpreadsheet\Reader\Csv::guessEncoding($file);
            $reader->setInputEncoding($encoding);
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setSheetIndex(0);
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $aData = [];
        foreach ($sheetData as $key => $value) {
            $aData[] = array_combine($sheetData[0], $sheetData[$key]);
        }
        array_splice($aData, 0, 1);

        return $aData;
    }

    public function runReportPickingBonus($init = null, $end = null)
    {
        try {
            setlocale(LC_ALL, 'es_ES');
            $byArea = Area::PICKING;
            $accountingFormat = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)';
            $locations = [];
            $initDate = ($init == null) ? Carbon::parse()->subDays(7)->format('Y-m-d') : Carbon::parse($init)->format('Y-m-d');
            $endDate = ($end == null) ? Carbon::parse()->subDays(1)->format('Y-m-d') : Carbon::parse($end)->format('Y-m-d');
            $prodEndDate = Carbon::parse($endDate)->addDays(1)->format('Y-m-d') . ' 05:59:59';
            $countDays = ($init != null) ? Carbon::parse($initDate)->diffInDays(Carbon::parse($endDate)) : 0;
            $pickingBonus = PickingBonus::where([
                ['bonus_date', '>=', $initDate],
                ['bonus_date', '<=', $endDate],
            ])
                ->join('associates', 'associates.id', '=', 'picking_bonus.associate_id')
                ->get();

            $productivity = PickingProductivity::where([
                ['init_picking', '>=', $initDate . ' 06:00:00'],
                ['end_picking', '<=', $prodEndDate],
            ])
                ->join('associates', 'associates.id', '=', 'picking_productivity.associate_id')
                ->select('associates.user_saalma', 'associates.name', 'wave_id', 'init_picking', 'end_picking', 'minutes', 'skus', 'boxes')
                ->orderBy('associate_id')
                ->orderBy('init_picking')
                ->get();

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getDefaultStyle()->getFont()->setSize(14);
            $sheet = $spreadsheet->getActiveSheet();

            $styleArray = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ];
            $fecha = Carbon::parse($endDate);
            $fecha->format("F");
            $mes = $fecha->formatLocalized('%B');
            $sheet->setCellValue('C1', 'PRODUCTIVIDAD LOCAL 10110');
            $sheet->setCellValue('C2', Carbon::parse($initDate)->format('d') . ' AL ' . Carbon::parse($endDate)->format('d') . ' DE ' . strtoupper($mes) . ' DEL ' . Carbon::parse($endDate)->format('Y'));
            $sheet->getStyle('C1:C2')->applyFromArray($styleArray);
            $sheet->setCellValue('A3', 'Num');
            $sheet->setCellValue('B3', 'Usuario');
            $sheet->setCellValue('C3', 'Nombre');
            $sheet->setCellValue('D3', 'Faltas');
            if ($countDays == 0) {
                $sheet->setCellValue('D2', Carbon::parse($initDate)->subDays(7)->format('Y-m-d'));
                $sheet->mergeCells('D2:E2');
                $sheet->setCellValue('F2', Carbon::parse($initDate)->subDays(6)->format('Y-m-d'));
                $sheet->mergeCells('F2:G2');
                $sheet->setCellValue('H2', Carbon::parse($initDate)->subDays(5)->format('Y-m-d'));
                $sheet->mergeCells('H2:I2');
                $sheet->setCellValue('J2', Carbon::parse($initDate)->subDays(4)->format('Y-m-d'));
                $sheet->mergeCells('J2:K2');
                $sheet->setCellValue('L2', Carbon::parse($initDate)->subDays(3)->format('Y-m-d'));
                $sheet->mergeCells('L2:M2');
                $sheet->setCellValue('N2', Carbon::parse($initDate)->subDays(2)->format('Y-m-d'));
                $sheet->mergeCells('N2:O2');
                $sheet->setCellValue('P2', Carbon::parse($initDate)->subDays(1)->format('Y-m-d'));
                $sheet->mergeCells('P2:Q2');
                $sheet->getStyle('A2:Q2')->applyFromArray($styleArray);
            } else {
                $col = 5;
                for ($i = 0; $i <= $countDays; $i++) {
                    $sheet->setCellValue($this->getCol($col) . '2', Carbon::parse($initDate)->addDays($i)->format('Y-m-d'));
                    $sheet->mergeCells($this->getCol($col) . '2:' . ($this->getCol($col + 1)) . '2');
                    $sheet->setCellValue($this->getCol($col) . '3', 'Cajas');
                    $sheet->setCellValue($this->getCol($col + 1) . '3', 'Bono');
                    $sheet->getStyle($this->getCol($col + 1))->getNumberFormat()->setFormatCode($accountingFormat);
                    $sheet->getStyle($this->getCol($col + 1))->getFont()->setBold(true);
                    $locations[Carbon::parse($initDate)->addDays($i)->format('Y-m-d')] = ['cell' => $col];
                    $col = $col + 2;
                }
                $sheet->getStyle('A2:' . $this->getCol($col) . '3')->applyFromArray($styleArray);
            }

            $rows = 4;
            $checks = [];

            $alignedArray = [];
            $count = 1;
            foreach ($pickingBonus as $bonus) {
                if (!isset($alignedArray[$bonus->employee_number])) {
                    $checkins = Checkin::where('associate_id', $bonus->associate_id)
                        ->whereBetween('checkin', [
                            $initDate . ' 00:00:00',
                            $endDate . ' 23:59:59'
                        ])->count();
                    $absences = $countDays - $checkins;
                    $alignedArray[$bonus->employee_number] = [
                        'user_saalma' => $bonus->user_saalma,
                        'nombre' => $bonus->name,
                        'ausencias' => $absences,
                        'dates' => [[
                            'date' => $bonus->bonus_date,
                            'cajas' => $bonus->boxes_shift,
                            'amount' => $bonus->bonus_amount,
                        ]],
                    ];
                } else {
                    $alignedArray[$bonus->employee_number]['dates'][] = [
                        'date' => $bonus->bonus_date,
                        'cajas' => $bonus->boxes_shift,
                        'amount' => $bonus->bonus_amount,
                    ];
                }
            }

            foreach ($alignedArray as $key => $arr) {
                $sheet->setCellValue('A' . $rows, $key);
                $sheet->setCellValue('B' . $rows, $arr['user_saalma']);
                $sheet->setCellValue('C' . $rows, $arr['nombre']);
                $sheet->setCellValue('D' . $rows, $arr['ausencias']);
                if ($arr['ausencias'] > 0) {
                    $sheet->getStyle('D' . $rows)
                        ->getFont()
                        ->getColor()
                        ->setRGB('FF0000');
                }
                foreach ($arr['dates'] as $bonus_date) {
                    $col = $locations[$bonus_date['date']]['cell'];
                    $sheet->setCellValue($this->getCol($col) . $rows, $bonus_date['cajas']);
                    $sheet->setCellValue($this->getCol($col + 1) . $rows, $bonus_date['amount']);
                }
                $rows++;
            }
            $col = 5;
            $color = 'D9D9D9D9';
            for ($i = 0; $i <= $countDays; $i++) {
                if ($color == 'D9D9D9D9') {
                    $sheet->getStyle($this->getCol($col) . "4:" . $this->getCol($col + 1) . ($rows - 1))
                        ->getFill()
                        ->setFillType('solid')
                        ->getStartColor()
                        ->setARGB($color);
                    $color = 'FFFFFFFF';
                } else {
                    $color = 'D9D9D9D9';
                }
                $col = $col + 2;
            }

            $prodSheet =  $spreadsheet->createSheet();

            $prodSheet->setCellValue('A1', 'Usuario');
            $prodSheet->setCellValue('B1', 'Nombre');
            $prodSheet->setCellValue('C1', 'Ola');
            $prodSheet->setCellValue('D1', 'Inicio Picking');
            $prodSheet->setCellValue('E1', 'Fin Picking');
            $prodSheet->setCellValue('F1', 'Minutos');
            $prodSheet->setCellValue('G1', 'Piezas');
            $prodSheet->setCellValue('H1', 'Cajas');
            $rows2 = 2;
            foreach ($productivity as $key => $prod) {
                $prodSheet->setCellValue('A' . $rows2, $prod->user_saalma);
                $prodSheet->setCellValue('B' . $rows2, $prod->name);
                $prodSheet->setCellValue('C' . $rows2, $prod->wave_id);
                $prodSheet->setCellValue('D' . $rows2, $prod->init_picking);
                $prodSheet->setCellValue('E' . $rows2, $prod->end_picking);
                $prodSheet->setCellValue('F' . $rows2, $prod->minutes);
                $prodSheet->setCellValue('G' . $rows2, $prod->skus);
                $prodSheet->setCellValue('H' . $rows2, $prod->boxes);
                $rows2++;
            }
            $prodSheet->setTitle('Productividad');
            foreach (range('A', 'H') as $columnID) {
                $prodSheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            $prodSheet->setAutoFilter('A1:H' . $rows2);
            $prodSheet->getStyle("A1:H1")
                ->getFill()
                ->setFillType('solid')
                ->getStartColor()
                ->setARGB('D9D9D9D9');
            $sheet->setTitle(Carbon::parse($initDate)->format('d') . ' AL ' . Carbon::parse($endDate)->format('d') . ' DE ' . strtoupper($mes));

            $sheet->getStyle('A3:' . $this->getCol($col - 1) . ($rows - 1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle('thin');

            $sheet->getColumnDimension('C')->setAutoSize(true);

            $sheet->freezePane('E4');

            $sPage = $spreadsheet->createSheet();
            $staff = Associate::where([
                ['area_id', '=', 1],
            ])->where(function ($q) {
                return $q
                    ->orWhere('subarea_id', '=', 2)
                    ->orWhere('subarea_id', '=', 6);
            })
                ->orderBy('subarea_id', 'desc')
                ->get();

            $sPage->setCellValue('B2', 'Núm. Emp.');
            $sPage->setCellValue('C2', 'Nombre');
            $sPage->setCellValue('D2', 'Fecha Ingreso');
            $sPage->setCellValue('E2', 'Faltas');
            $sPage->setCellValue('F2', 'Posición');
            $sPage->setCellValue('G2', 'Bono');
            $rows3 = 3;
            $sPage->getStyle('G')->getNumberFormat()->setFormatCode($accountingFormat);
            foreach ($staff as $key => $st) {
                $checkins = Checkin::where('associate_id', $st->id)
                    ->whereBetween('checkin', [
                        $initDate . ' 00:00:00',
                        $endDate . ' 23:59:59'
                    ])->count();
                $absences = $countDays - $checkins;

                $sPage->setCellValue('B' . $rows3, $st->employee_number);
                $sPage->setCellValue('C' . $rows3, $st->name);
                $sPage->setCellValue('D' . $rows3, Carbon::parse($st->entry_date)->format('Y-m-d'));
                $sPage->setCellValue('E' . $rows3, $absences);
                $sPage->setCellValue('F' . $rows3, $st->subarea->name);
                $sPage->setCellValue('G' . $rows3, $st->subarea_id == 2 ? 400 : 300);
                if ($absences > 0) {
                    $sPage->getStyle('E' . $rows3)
                        ->getFont()
                        ->getColor()
                        ->setRGB('FF0000');
                }
                $rows3++;
            }
            $sPage->getStyle("B2:G2")
                ->getFill()
                ->setFillType('solid')
                ->getStartColor()
                ->setARGB('CCECFF');
            $sPage->getStyle('B2:G' . ($rows3 - 1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle('thin')
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('blue'));

            $sPage->getColumnDimension('B')->setAutoSize(true);
            $sPage->getColumnDimension('C')->setAutoSize(true);
            $sPage->getColumnDimension('D')->setAutoSize(true);
            $sPage->getColumnDimension('E')->setAutoSize(true);
            $sPage->getColumnDimension('F')->setAutoSize(true);
            $sPage->getColumnDimension('G')->setAutoSize(true);
            $sPage->getStyle('B2:G2')->applyFromArray($styleArray);
            $sPage->setTitle('SEMANAL');
            $response = response()->streamDownload(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="your_file.xlsx"');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response->send();
        } catch (\Exception $exception) {
            return $exception;
            //new Log::error($exception->getMessage());
            return false;
        }
    }

    /**
     * Carga la productividad de un excel (ya convertido en json) del área de picking
     * @param $array
     * @return mixed
     */
    public function loadPickingProductivity($array)
    {
        //Redis::get('saalmausers:'.$arr["Usuario / Order Picker"])
        $allProd = [];
        $now = Carbon::now()->toDateTimeString();
        foreach ($array as $arr) {
            if (isset($arr["Número de Ola"])) {
                $allProd[] = [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'associate_id' => Redis::get('saalmausers:' . $arr["Usuario / Order Picker"]) ?? 1000,
                    'wave_id' => $arr["Número de Ola"],
                    'init_picking' => Carbon::createFromFormat('m/d/y h:i a', $arr["Hora Inicio Surtido"]),
                    'end_picking'  => Carbon::createFromFormat('m/d/y h:i a', $arr["Hora Finalización"]),
                    'minutes'  => (float)$arr["Tiempo Real"],
                    'skus'     => $arr["Total Skus Surtidos"],
                    'boxes'    => $arr["Total Cajas Surtidas"]
                ];
            }
        }
        if (count($allProd) > 0) {
            PickingProductivity::upsert($allProd, ['associate_id', 'wave_id', 'init_picking'], ['created_at', 'updated_at', 'end_picking', 'minutes', 'skus', 'boxes']);
            $this->calculatePickingBonus($now);
            return ['message' => 'Archivo cargado correctamente', 'typeMessage' => 'success', 'date' => $allProd[0]['init_picking']];
        } else {
            return ['message' => 'El archivo no corresponde a una productividad', 'typeMessage' => 'error'];
        }
    }

    public function calculatePickingBonus($now)
    {
        $prodQuery = PickingProductivity::where('created_at', '>=', $now);

        $prods = PickingProductivity::where('created_at', '>=', $now)
            ->orderBy('associate_id')
            ->orderBy('init_picking')
            ->get();
        $pickingBonus = [];

        foreach ($prods as $key => $prod) {
            if ($prod->associate_id != 1000) {
                $check = Checkin::where('associate_id', $prod->associate_id)
                    ->where('checkout', '>=', $prod->init_picking)
                    ->where('checkin', '<=', $prod->end_picking)
                    ->first();

                if (empty($check)) {
                    $newCheck = new Checkin;
                    $newCheck->checkin = $prod->init_picking;
                    $time1 = Carbon::parse($prod->init_picking);
                    for ($i = 1; $i <= 5; $i++) {
                        if (isset($prods[$key + $i])) {
                            if ($prods[$key + $i]->associate_id == $prod->associate_id) {
                                $time2 = Carbon::parse($prods[$key + $i]->init_picking);
                                if ($time1->diffInHours($time2) > 12 || $prods[$key + ($i - 1)]->end_picking > $prods[$key + $i]->end_picking) {
                                    $newCheck->checkout = $prods[$key + ($i - 1)]->end_picking;
                                    break;
                                }
                            } else {
                                $newCheck->checkout = $prods[$key + ($i - 1)]->end_picking;
                                break;
                            }
                        } else {
                            $newCheck->checkout = $prods[$key + ($i - 1)]->end_picking;
                            break;
                        }
                    }

                    $newCheck->associate_id = $prod->associate_id;
                    $newCheck->status = 1;
                    $newCheck->user_id = 1;
                    $newCheck->save();
                }
            }
        }

        $fechas = $prodQuery->select(DB::raw('date(MIN(init_picking)) as inicio'), DB::raw('date(MAX(end_picking)) as fin'))->first();

        $checkin = Checkin::whereBetween('checkin', [
            $fechas->inicio . ' 00:00:00',
            $fechas->fin . ' 23:59:59'
        ])->get();

        $bonusBoard = Board::where('area_id', 1)
            ->where('subarea_id', 1)
            ->get();

        foreach ($checkin as $key => $ch) {
            $bonusQuery = PickingProductivity::select('associate_id', DB::raw('sum(boxes) as boxes'), DB::raw('date(min(init_picking)) as bonus_date'))->where([['associate_id', '=', $ch->associate_id], ['init_picking', '>=', $ch->checkin], ['end_picking', '<=', $ch->checkout]])->groupBy('associate_id')->first();

            if (!empty($bonusQuery)) {
                $bonusAmount = $ch->associate->subarea_id == 1 ? $bonusBoard->where('quantity', '<=', $bonusQuery->boxes)->sortByDesc('quantity')->first() : 0;
                $pbo = [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'associate_id' => $ch->associate_id,
                    'bonus_date' => $bonusQuery->bonus_date,
                    'boxes_shift' => $bonusQuery->boxes,
                    'bonus_amount' => $bonusAmount->bono ?? 0
                ];
                $pickingBonus[] = PickingBonus::upsert($pbo, ['associate_id', 'bonus_date'], ['boxes_shift', 'bonus_amount', 'updated_at', 'created_at']);
            }
        }
    }

    private function getHours($unionized, $hours, $assignHours)
    {
        if ((bool)$unionized === false) {
            $extra =  $hours - $assignHours < 0 ? round($hours - $assignHours): floor($hours - $assignHours);
        } else {
            $decimal = number_format($hours - $assignHours, 1);
            $extra = ($decimal < 0) ? $decimal : (floor((($decimal * 100)) / 50) * 50) / 100;
        }
        return $extra;
    }


    private function getCol($num)
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($num);
    }
}
