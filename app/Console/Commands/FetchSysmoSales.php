<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SettingsDatabase;
use Illuminate\Support\Facades\DB;

class FetchSysmoSales extends Command
{
    protected $signature = 'fetch:sysmo-sales';
    protected $description = 'Get sales from Sysmo API to all SM Customers';

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
            ->where('user_erp', 'sysmo')
            ->get()
            ->toArray();

        if($appUsersTable){
            foreach ($appUsersTable as $appId) {
                $databaseId = $appId->ID;

                SettingsDatabase::updateSalesFromSysmo(date('Y-m'), $databaseId);
            }
        }
    }
}
