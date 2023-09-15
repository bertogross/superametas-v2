<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RunDefaultSchema extends Migration
{
    public function up()
    {
        // Replace 'default_schema.sql' with the path to your SQL script file.
        $sql = file_get_contents(database_path('default_schema/tenancy.sql'));

        DB::unprepared($sql);
    }

    public function down()
    {
        // If needed, define a rollback action here.
    }
}
