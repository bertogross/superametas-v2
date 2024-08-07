<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * Example: php artisan make:command FetchZohoMail
     *
     * https://laravel.com/docs/10.x/scheduling
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected function schedule(Schedule $schedule)
    {
        /****************************************
         * START routine for smOnboard database
         ****************************************/
        // Update app_api table with actual IPCA data to smOnboard database
        // to test in linux server terminal: php artisan fetch:ipca
        $schedule->command('fetch:ipca')->daily(); // Run the task daily

        // Check stripe customers payment status and update 'app_users' table 'user_stripe_subscription_status' collum (if webhook is losted)
            // TODO

        /****************************************
         * END routine for smOnboard database
         ****************************************/


        /****************************************
         * START routine for Customer databases
         ****************************************/
        // https://laravel.com/docs/10.x/scheduling
        // Update surveys and tasks to each smApp(ID) database
        // to test in linux server terminal: php artisan fetch:surveys
        $schedule->command('fetch:surveys')
            ->dailyAt('01:00')
            ->timezone('America/Sao_Paulo');

        // Update wlsm_sales table for smApp(ID) database
        // to test in linux server terminal: php artisan fetch:sysmo-sales
        $schedule->command('fetch:sysmo-sales')
            //->everyThirtyMinutes()
            //->between('7:00', '23:30');
            ->everyFifteenMinutes()
            ->hourly()
            ->timezone('America/Sao_Paulo')
            ->between('7:00', '23:45');
        $schedule->command('fetch:sysmo-sales')
            ->dailyAt('23:59')
            ->timezone('America/Sao_Paulo');


        // Send mail messa notification for Goals to each smApp(ID) database
        // to test in linux server terminal: php artisan fetch:zoho-goals-mail
        $schedule->command('fetch:zoho-goals-mail')
            ->weeklyOn(1, '6:00')
            ->timezone('America/Sao_Paulo');
        $schedule->command('fetch:zoho-goals-mail')
            ->weeklyOn(4, '6:00')
            ->timezone('America/Sao_Paulo');

        /****************************************
         * END routine for Customer databases
         ****************************************/
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
