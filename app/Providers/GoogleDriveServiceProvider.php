<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google_Client;
use Google_Service_Drive;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Google_Client::class, function ($app) {
            $client = new Google_Client();
            $client->setClientId(config('google.client_id'));
            $client->setClientSecret(config('google.client_secret'));
            $client->setRedirectUri(config('google.redirect_uri'));
            $client->setScopes(config('google.scopes'));
            return $client;
        });

        $this->app->singleton(Google_Service_Drive::class, function ($app) {
            $client = $app->make(Google_Client::class);
            return new Google_Service_Drive($client);
        });
    }
}
