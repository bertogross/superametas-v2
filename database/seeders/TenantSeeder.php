<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
//use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    protected $userID;

    /**
     * Create a new seeder instance.
     *
     * @param int $userId
     */
    public function __construct(int $userID)
    {
        $this->userID = $userID;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve the user ID passed as an argument
        $userID = $this->userID;

        // Insert a tenant record
        /*DB::table('tenants')->insert([
            'id' => ''.$userID.'',
            'created_at' => now(),
            'updated_at' => now(),
            'data' => json_encode(['tenancy_db_name' => 'tenantApp'.$userID.'']),
        ]);*/

        // RELATED TO TENANCY FOR LARAVEL
        $tenant = \App\Models\Tenant::create(['id' => 'App'.$userID.'']);
        $tenant->domains()->create(['domain' => 'App'.$userID.'.localhost']);

        /*
        \App\Models\Tenant::all()->runForEach(function () {
            \App\Models\User::factory()->create();
        });
        */

    }

}
