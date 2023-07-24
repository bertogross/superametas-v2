<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedTinyInteger('role')->nullable()->default(null);
            $table->text('avatar');
            $table->rememberToken();
            $table->timestamps();
        });
        User::create([
            'name' => 'admin',
            'email' => 'bertogross@gmail.com',
            'password' => Hash::make('12345678gross'),
            'email_verified_at' => null,
            'role' => 1,
            'avatar' => 'avatar-1.jpg',
            'created_at' => now()
        ]);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
