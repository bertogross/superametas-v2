<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ZohoController;

class FetchZohoGoalsMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:zoho-goals-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail notification with reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $OnboardConnection = DB::connection('smOnboard');

        // Get the list of database names from app_users
        $appUsersTable = $OnboardConnection->table('app_users')
            ->get()
            ->toArray();

        if($appUsersTable){
            foreach ($appUsersTable as $appId) {
                $databaseId = $appId->ID;

                ZohoController::sendGoalsEmail($databaseId);
            }
        }
    }
}
