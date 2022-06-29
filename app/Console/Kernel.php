<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('report:hours-picking')->wednesdays()->at('07:00');
        $schedule->command('crc:productivity_sorter')->wednesdays()->at('06:00');
        $schedule->command('crc:productivity_staff_sorter')->wednesdays()->at('06:00');
        $schedule->command('crc:change_shift')->daily()->at('00:01');
        $schedule->command('crc:validate_entry_date_associates')->daily()->at('00:01');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
