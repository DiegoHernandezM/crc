<?php

namespace App\Managers;

use App\Models\UserReport;
use App\Repositories\ReportRepository;
use App\Repositories\SorterReportRepository;
use App\Repositories\UserReportRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Carbon\Carbon;
use App\Models\Log as Logger;

class SendMailsManager
{
    protected $reportRepository;
    protected $userReportsRepository;
    protected $sorterReportRepository;

    public function __construct()
    {
        $this->reportRepository = new ReportRepository();
        $this->userReportsRepository = new UserReportRepository();
        $this->sorterReportRepository = new SorterReportRepository();
    }

    public function sendMailReportHoursPicking()
    {
        try {
            $today = Carbon::parse("last Wednesday");
            $dateInit = Carbon::parse($today)->subDays(7)->format('Y-m-d');
            $endDay = Carbon::parse($today)->subDays(1)->format('Y-m-d');

            $file = $this->reportRepository->runReportHours($dateInit, $endDay, \App\Models\Area::PICKING, true);

            if (!$file === false) {
                $sendTo = [];
                $users = $this->getUsersReport();

                foreach ($users as $user) {
                    $subscribes = json_decode($user->subscrited_to);
                    foreach ($subscribes as $subscribe) {
                        if ($subscribe === UserReport::PKHOURS) {
                            $sendTo[] = $user->email;
                        }
                    }
                }
                $this->buildMail($sendTo, 'Reporte horas extra', $file, 'Se adjunta el reporte de Horas Extra. Saludos.');
                unlink(public_path('files/'.$file.'.xlsx'));
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

    public function sendMailProductivitySorter()
    {
        try {
            $today = Carbon::parse("last Wednesday");
            $dateInit = Carbon::parse($today)->subDays(7)->format('Y-m-d');
            $endDay = Carbon::parse($today)->subDays(1)->format('Y-m-d');

            $file = $this->sorterReportRepository->runReportBonus($dateInit, $endDay, true);

            if (!$file === false) {
                $sendTo = [];
                $users = $this->getUsersReport();

                foreach ($users as $user) {
                    $subscribes = json_decode($user->subscrited_to);
                    foreach ($subscribes as $subscribe) {
                        if ($subscribe === UserReport::SRTPROD) {
                            $sendTo[] = $user->email;
                        }
                    }
                }
                $this->buildMail($sendTo, 'Reporte produtividad sorter', $file, 'Se adjunta el reporte de Productividad de Sorter. Saludos.');
                unlink(public_path('files/'.$file.'.xlsx'));
            }
            return true;
        } catch(\Exception $e) {
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

    public function sendMailProductivityStaffSorter()
    {
        try {
            $today = Carbon::parse("last Wednesday");
            $dateInit = Carbon::parse($today)->subDays(7)->format('Y-m-d');
            $endDay = Carbon::parse($today)->subDays(1)->format('Y-m-d');

            // Procesa productiviad de staff y managers
            $yearWeek = Carbon::parse($today)->year.''.Carbon::parse($today)->startOfWeek()->weekOfYear;
            $this->sorterReportRepository->processBonusStaff($yearWeek);

            $file = $this->sorterReportRepository->runReportBonusStaff($dateInit, $endDay, true);
            if (!$file === false) {
                $sendTo = [];
                $users = $this->getUsersReport();

                foreach ($users as $user) {
                    $subscribes = json_decode($user->subscrited_to);
                    foreach ($subscribes as $subscribe) {
                        if ($subscribe === UserReport::SRTPROD) {
                            $sendTo[] = $user->email;
                        }
                    }
                }
                $this->buildMail($sendTo, 'Reporte produtividad STAFF sorter', $file, 'Se adjunta el reporte de Productividad de Staff Sorter. Saludos.');
                unlink(public_path('files/'.$file.'.xlsx'));
            }
            return true;
        } catch(\Exception $e) {
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

    private function getUsersReport()
    {
        $users = $this->userReportsRepository->getUsersMails();
        return $users;
    }

    private function buildMail($to, $subject, $fileName, $body)
    {
        try {
            Mail::send([], [], function (Message $message) use ($to, $subject, $fileName, $body) {
                $message
                    ->to($to)
                    ->from('amartinezw@agarcia.com.mx', 'Abraham Martinez')
                    ->subject($subject)
                    ->setBody($body, 'text/html');
                $message->attach(public_path('files/'.$fileName.'.xlsx'));
            });
        } catch (\Exception $e) {
            $logData = [
                'message'       => $e->getMessage(),
                'loggable_id'   => Logger::LOG_MAIL,
                'loggable_type' => 'Mail',
                'user_id'       => 1,
            ];
            $log = new Logger();
            $log->create($logData);
        }
    }
}