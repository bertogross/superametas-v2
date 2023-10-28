<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Dropbox\Client as DropboxClient;
use App\Http\Controllers\SettingsAccountController;

class DropboxController extends Controller
{
    protected $settingsAccountController;

    public function __construct(SettingsAccountController $settingsAccountController)
    {
        $this->settingsAccountController = $settingsAccountController;
    }

    public function files()
    {
        $dropboxToken = getDropboxToken('dropbox_token');

        if (!$dropboxToken) {
            return redirect(route('DropboxFilesURL'))->with('error', 'Please connect to Dropbox first.');
        }

        $dropbox = new DropboxClient($dropboxToken);
        $response = $dropbox->listFolder('/');

        if (!isset($response['entries'])) {
            return redirect(route('DropboxFilesURL'))->with('error', 'Failed to retrieve files and folders from Dropbox.');
        }

        $items = $response['entries'];

        $files = [];
        $folders = [];

        foreach ($items as $item) {
            if ($item['.tag'] === 'file') {
                $item['link'] = $dropbox->getTemporaryLink($item['path_display']);
                $files[] = $item;
            } elseif ($item['.tag'] === 'folder') {
                $folderContents = $dropbox->listFolder($item['path_display']);
                $folderSize = 0;
                $fileCount = 0;

                while (true) {
                    foreach ($folderContents['entries'] as $folderItem) {
                        if ($folderItem['.tag'] === 'file') {
                            $folderSize += $folderItem['size'];
                            $fileCount++;
                        }
                    }

                    if (!$folderContents['has_more']) {
                        break;
                    }

                    $folderContents = $dropbox->listFolderContinue($folderContents['cursor']);
                }
                $folderSizeGB = $folderSize / (1024 ** 3);
                $item['size'] = number_format($folderSizeGB, 2);
                $item['file_count'] = $fileCount;
                $folders[] = $item;
            }
        }

        $storageInfo = $this->getStorageInfo($dropboxToken);

        return view('settings.files', compact('files', 'folders', 'storageInfo'));
    }

    private function getStorageInfo($dropboxToken)
    {
        $spaceInfo = $this->getSpaceUsage($dropboxToken);
        //dd($spaceInfo);

        if (!is_array($spaceInfo) || !isset($spaceInfo['used'])) {
            throw new \Exception('Failed to retrieve storage information from Dropbox.');
        }

        $used = $spaceInfo['used'] ? $spaceInfo['used'] / (1024 * 1024 * 1024) : 0; // Convert to GB
        $total = $spaceInfo['allocation'] ? $spaceInfo['allocation']['allocated'] / (1024 * 1024 * 1024) : 0; // Convert to GB
        $percentageUsed = $used > 0 && $total > 0 ? ($used / $total) * 100 : 0;

        return [
            'total' => number_format($total, 2),
            'used' => number_format($used, 2),
            'percentageUsed' => number_format($percentageUsed, 2),
        ];
    }

    //https://dropbox.github.io/dropbox-api-v2-explorer/#users_get_space_usage
    //https://www.dropbox.com/developers/documentation/http/documentation#users-get_space_usage
    public function getSpaceUsage($dropboxToken)
    {
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('POST', 'https://api.dropboxapi.com/2/users/get_space_usage', [
            'headers' => [
                'Authorization' => 'Bearer ' . $dropboxToken,
                //'Content-Type' => 'application/json',
            ],
        ]);

        $data = $response ? json_decode($response->getBody(), true) : '';

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('JSON decode error: ' . json_last_error_msg());
            return null;
        }

        \Log::info('Dropbox API response: ' . print_r($data, true));
        return $data;
    }

    public function deleteFile($path)
    {
        $dropboxToken = getDropboxToken('dropbox_token');

        if (!$dropboxToken) {
            return redirect(route('DropboxFilesURL'))->with('error', 'Please connect to Dropbox first.');
        }

        $dropbox = new DropboxClient($dropboxToken);

        try {
            $dropbox->delete($path);
            return redirect(route('DropboxFilesURL'))->with('success', 'File deleted successfully.');
        } catch (\Exception $e) {
            return redirect(route('DropboxFilesURL'))->with('error', 'Failed to delete file.');
        }
    }


    public function callback(Request $request)
    {
        $appKey = config('services.dropbox.app_key');
        $appSecret = config('services.dropbox.app_secret');
        $redirectUri = route('DropboxCallbackURL');

        if (!$request->has('code')) {
            return redirect(route('DropboxFilesURL'))->with('error', 'Authorization failed. Please try again.');
        }

        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->post('https://api.dropboxapi.com/oauth2/token', [
            'form_params' => [
                'code' => $request->input('code'),
                'grant_type' => 'authorization_code',
                'client_id' => $appKey,
                'client_secret' => $appSecret,
                'redirect_uri' => $redirectUri,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['access_token'])) {
            return redirect(route('DropboxFilesURL'))->with('error', 'Authorization failed. Please try again.');
        }

        $this->settingsAccountController->updateOrInsertSetting('dropbox_token', $data['access_token']);

        return redirect(route('DropboxFilesURL'))->with('success', 'Dropbox has been authorized successfully.');
    }

    public function authorizeDropbox()
    {
        $appKey = config('services.dropbox.app_key');
        $redirectUri = route('DropboxCallbackURL');

        $authUrl = "https://www.dropbox.com/oauth2/authorize?client_id={$appKey}&response_type=code&redirect_uri={$redirectUri}";

        return redirect($authUrl);
    }

    public function deauthorizeDropbox()
    {
        $this->settingsAccountController->updateOrInsertSetting('dropbox_token', null);

        return redirect(route('DropboxFilesURL'))->with('success', 'Dropbox has been deauthorized successfully.');
    }
}
