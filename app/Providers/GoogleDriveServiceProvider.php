<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use League\Flysystem\Filesystem;

class GoogleDriverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Storage::extend("google", function ($app, $config) {
            // google client
            $client = new \Google_Client;
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            // google drive client
            $service = new \Google_Service_Drive($client);

            // google drive adapter filesystem
            $adapter = new GoogleDriveAdapter($service, $config['folderId']);
            return new FileSystem($adapter);
        });
    }
}
