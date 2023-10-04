<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('users')->insert([
        $InsertedID = DB::table('users')->insertGetId([ // Insert data into the 'users' table and retrieve the ID
            'name' => 'Daniel 2',
            'email' => 'bertogross2@gmail.com',
            'password' => Hash::make('12345678gross'),
            'avatar' => 'media/uploads/avatars/avatar-1.jpg',
            'subdomain' => 'daniel', // RELATED TO TENANCY FOR LARAVEL
        ]);


        // RELATED TO TENANCY FOR LARAVEL
        // Call the TenantSeeder and pass the user ID as a parameter
        $tenantSeeder = app(TenantSeeder::class, ['userID' => $InsertedID]);

        // Call the TenantSeeder
        $tenantSeeder->run();
    }
}
