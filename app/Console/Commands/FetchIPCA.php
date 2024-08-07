<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SettingsDatabase;

class FetchIPCA extends Command
{
    protected $signature = 'fetch:ipca';
    protected $description = 'Execute to get the IPCA data from updateIPCAfromIBGE function';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SettingsDatabase::updateIPCAfromIBGE();
    }
}
