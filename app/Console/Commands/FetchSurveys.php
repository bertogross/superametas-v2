<?php

namespace App\Console\Commands;

use App\Models\Survey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchSurveys extends Command
{
    protected $signature = 'fetch:surveys';
    protected $description = 'Chack survey status and populate user tasks';

    public function __construct()
    {
        parent::__construct();
    }

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

                Survey::populateSurveys($databaseId);
            }
        }
    }
}
