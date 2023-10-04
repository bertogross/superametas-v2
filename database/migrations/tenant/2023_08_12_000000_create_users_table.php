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
        /*
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedTinyInteger('role')->nullable();
            $table->text('avatar')->nullable();
            $table->text('erp')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        */


        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->text('avatar')->nullable();
            $table->string('role', 10)->default(1);
            //$table->string('subdomain', 200)->unique(); // RELATED TO TENANCY FOR LARAVEL
            $table->rememberToken();
            $table->string('erp', 200)->nullable();
            $table->longText('erp_data')->nullable();
            $table->string('ip', 100)->nullable();
            $table->unsignedTinyInteger('network_status')->default(1);
            $table->string('activation_key', 255)->default('0');
            $table->string('redefinition_key', 10)->default('0');
            $table->bigInteger('disk_quota')->default(33554432);
            $table->bigInteger('limit_of_records')->default(5);
            $table->string('stripe_customer_id', 100)->nullable();
            $table->longText('stripe_products')->nullable();
            $table->string('stripe_subscription_id', 100)->nullable();
            $table->string('stripe_subscription_status', 20)->default('trialing');
            $table->unsignedTinyInteger('stripe_subscription_quantity')->default(0);
            $table->text('additional_data')->nullable();
            $table->timestamps();
        });

    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('users');
        Schema::dropIfExists('users');
    }
}
