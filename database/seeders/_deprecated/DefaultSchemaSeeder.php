<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultSchemaSeeder extends Seeder
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

    
    public function run()
    {
        $sql = file_get_contents(database_path('default_schema/tenancy.sql'));

        DB::unprepared($sql);
    }
}
