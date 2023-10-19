<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Drive;

class SettingsStorageController extends Controller
{
    private $client;
    private $clientId;
    private $clientSecret;
    private $redirectUri;

    /**
     * Constructor to set up the Google Client and configurations.
     *
     * @param Google_Client $client - Injected Google Client instance.
     */
    public function __construct(Google_Client $client)
    {
        $this->client = $client;

        // Fetching configurations from environment variables.
        $this->clientId = env('GOOGLE_DRIVE_CLIENT_ID');
        $this->clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');
        $this->redirectUri = env('GOOGLE_DRIVE_REDIRECT_URI');

        // Setting up the client with the configurations.
        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri($this->redirectUri);
        $this->client->addScope(Google_Service_Drive::DRIVE);
    }

    /**
     * Index method to list files from Google Drive.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Setting the access token for the client from the session.
        $this->client->setAccessToken(session('google_drive_token')); // Assuming you've stored the token in session after OAuth2 flow

        // Creating a Drive service instance to interact with the API.
        $driveService = new Google_Service_Drive($this->client);

        // Fetching the list of files.
        $files = $driveService->files->listFiles();

        // Returning the view with the list of files.
        return view('settings-storage', ['files' => $files->getFiles()]);
    }

    /**
     * OAuth callback method to handle the authentication flow.
     *
     * @param Request $request - The incoming request instance.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function oauthCallback(Request $request)
    {
        // Checking if the request has the 'code' parameter (OAuth2 code).
        if ($request->has('code')) {
            // Authenticating the client with the received code.
            $this->client->authenticate($request->input('code'));

            // Storing the access token in the session.
            session(['google_drive_token' => $this->client->getAccessToken()]);

            // Redirecting back to the storage settings page.
            return redirect()->route('settings.storage');
        }

        // Redirecting back with an error if something went wrong.
        return redirect()->route('settings.storage')->with('error', 'Something went wrong');
    }
}
