<?php

namespace App\Http\Controllers;

use App\Models\Zoho;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

/**
 * FIRST
 * To obtain the first token to stored in database, is necessary going through the initial OAuth authorization flow. The refresh token is obtained when you first authenticate your application with Zoho using the OAuth process and store in database. Here's the URL:
 * https://accounts.zoho.com/oauth/v2/auth?scope=ZohoMail.messages.CREATE&client_id=1000.Q06X2UZJHS84R74E7EPEGEYBP71C4N&response_type=code&access_type=offline&redirect_uri=http://localhost:8000/api/zoho/callback
 * https://accounts.zoho.com/oauth/v2/auth?scope=ZohoMail.messages.CREATE&client_id=1000.Q06X2UZJHS84R74E7EPEGEYBP71C4N&response_type=code&access_type=offline&redirect_uri=https://development.superametas.com/api/zoho/callback
 */

class ZohoController extends Controller
{
    public function callback(Request $request)
    {
        $code = $request->input('code');

        // Exchange the authorization code for an access token
        $accessToken = ZohoController::exchangeCodeForAccessToken($code);

        return "Authorization successful! Access Token: {$accessToken}";
    }

    public static function exchangeCodeForAccessToken($code)
    {
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');
        $redirectUri = env('ZOHO_REDIRECT_URI');

        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token';

        $tokenData = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $response = Http::asForm()->post($tokenUrl, $tokenData);

        if ($response->getStatusCode() === 200) {
            $responseData = $response->json();

            if (isset($responseData['access_token'])) {
                // Store the access token as needed
                ZohoController::updateToken($responseData, $code);

                return $responseData['access_token'];
            } else {
                // Log the response for debugging
                \Log::error('exchangeCodeForAccessToken responseData Error:', $responseData);
                return null;
            }
        } else {
            // Handle token retrieval error
            \Log::error('exchangeCodeForAccessToken Error:', $response->getBody());
            return null;
        }
    }

    public static function updateToken($zohoJson, $code)
    {
        if (!empty($zohoJson)) {
            $existingRecord = DB::connection('smOnboard')->table('app_api')
                ->where('api_origin', 'zoho')
                ->first();

            if ($existingRecord) {
                // Update existing record
                DB::connection('smOnboard')->table('app_api')
                    ->where('api_origin', 'zoho')
                    ->update(['api_data' => $zohoJson, 'authorization_code' => $code, 'updated_at' => now()]);
                $message = 'Data updated';
            } else {
                // Create new record
                DB::connection('smOnboard')->table('app_api')
                    ->insert(['api_origin' => 'zoho', 'api_data' => $zohoJson, 'authorization_code' => $code, 'created_at' => now(), 'updated_at' => now()]);
                $message = 'Data created';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data retrieved from API or data format is incorrect'
            ]);
        }
    }

    /*
    public static function getZohoAccessToken()
    {
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');
        $redirectUri = env('ZOHO_REDIRECT_URI');
        $authorizationCode = ZohoController::getAuthorizationCode();

        if(!$authorizationCode){
            \Log::error('authorizationCode Error:', 'Empty code');
            return null;
        }

        // Step 1: Request an OAuth 2.0 authorization code
        $authorizationUrl = "https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&scope=ZohoMail.messages.CREATE";

        // Step 2: Exchange the authorization code for an access token
        $tokenUrl = "https://accounts.zoho.com/oauth/v2/token";

        $tokenData = [
            'code' => $authorizationCode,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $response = Http::asForm()->post($tokenUrl, $tokenData);

        if ($response->getStatusCode() === 200) {
            $responseData = $response->json();

            if (isset($responseData['access_token'])) {
                ZohoController::updateToken($responseData, $code);

                return $responseData['access_token'];
            } else {
                // Log the response for debugging
                \Log::error('getZohoAccessToken responseData Error:', $responseData);
                return null;
            }
        } else {
            // Log the response for debugging
            \Log::error('getZohoAccessToken Error:', $response->getBody());
            return null;
        }
    }
    */
    public static function getZohoAccessToken()
    {
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');
        $redirectUri = env('ZOHO_REDIRECT_URI');
        $authorizationCode = ZohoController::getAuthorizationCode();

        if (!$authorizationCode) {
            \Log::error('authorizationCode Error: Empty code');
            return null;
        }

        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token';

        $tokenData = [
            'form_params' => [
                'code' => $authorizationCode,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ],
        ];

        try {
            $client = new Client();
            $response = $client->post($tokenUrl, $tokenData);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            if ($statusCode === 200 && isset($responseData['access_token'])) {
                ZohoController::updateToken($responseData, $authorizationCode);
                return $responseData['access_token'];
            } else {
                \Log::error('getZohoAccessToken responseData Error:', $responseData);
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('getZohoAccessToken Exception: ' . $e->getMessage());
            return null;
        }
    }

    public static function getAuthorizationCode()
    {
        try {
            $OnboardConnection = DB::connection('smOnboard');
            $result = $OnboardConnection->table('app_api')->where('api_origin', 'zoho')->first();

            if (!$result) {
                \Log::error('getAuthorizationCode is empty');

                return '';
            }

            return $result->authorization_code ?? null;

        } catch (\Exception $e) {
            // Log the error message
            \Log::error($e->getMessage(), ['method' => __METHOD__, 'file' => __FILE__, 'line' => $e->getLine()]);

            return '';
        }

        return null;
    }

    public static function getZohoAccountData($accessToken)
    {
        // Make an authenticated API request to fetch account details
        try {
            $response = Http::withToken($accessToken)->get('https://mail.zoho.com/api/accounts');

            $responseData = $response->json();

            if ($response->getStatusCode() === 200) {
                return $responseData;
            } else {
                \Log::error('getZohoAccountData responseData Error:', $responseData);
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('getZohoAccountData Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * MONDAY Routine
     * STANDBY = -Envia relatório de despesas que tenham como base contas com mínimo de R$ 5.000 e que estejam no mínimo 20% maior que o planejado do mês.
     * -Envia relatório com receitas dos setores que estejam 20% abaixo até o dia do lançamento do relatório em relação ao acumulado do mês.
     * -Envia relatório com receitas dos setores que estejam 20% acima até o dia do lançamento do relatório em relação ao acumulado do mês.
     * -Obs.: Não enviar relatório dia 1 de cada mês se cair na segunda ou na quinta.
     *
     * PS.: run cron after 00:01AM
     *
     * Make test via terminal with command:
     * wget "http://localhost:8000/api/zoho/send-goals-email/1"
     * wget "https://development.superametas.com/api/zoho/send-goals-email/1"
     *
     * wget --header="Authorization: Bearer YOUR_API_TOKEN" "https://development.superametas.com/api/zoho/send-goals-email/1"
     */
    public function sendGoalsEmail($database = null)
    {
        /*
        $today = now();

        // Don't send report on the 1st of each month if it falls on Monday or Thursday
        if ($today->day == 1 && ($today->isMonday() || $today->isThursday())) {
            return response()->json(['message' => 'Email not sent due to date conditions'], 200);
        }

        // Prevent to send only on Monday and Thursday
        if (!$today->isMonday() && !$today->isThursday()) {
            return response()->json(['message' => 'Email not sent. Today is not Monday or Thursday'], 200);
        }
        */

        if (!$database) {
            return response()->json(['error' => 'Database not specified'], 400);
        }

        // Set the database connection name dynamically
        $databaseName = 'smApp' . $database;
        $databaseExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
        if (!$databaseExists) {
            return null;
        }

        config(['database.connections.smAppTemplate.database' => $databaseName]);

        // Get the Zoho OAuth token through your own authentication process
        $accessToken = ZohoController::getZohoAccessToken();

        if(!$accessToken){
            return 'Failed to accessToken.';
        }

        // Make an authenticated API request to fetch account details
        $accountData = ZohoController::getZohoAccountData($accessToken);

        if (!$accountData) {
            \Log::error('Failed to fetch Zoho account data');

            return 'Failed to fetch Zoho account data.';
        }

        // Extract the account_id from the response
        $accountId = $accountData['data'][0]['account_id'];

        $zohoApiUrl = "https://mail.zoho.com/api/accounts/{$accountId}/messages";
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');

        $client = new Client();

        $authHeader = [
            'Authorization' => "Zoho-oauthtoken {$clientId}:{$clientSecret}",
        ];

        // Define your email data here
        $emailData = [
            'fromAddress' => env('ZOHO_CURRENT_USER_EMAIL'),
            'toAddress' => 'bertogross@gmail.com',
            'subject' => 'Daily Email',
            'content' => 'Your daily email content here.',
        ];

        try {
            $response = $client->post($zohoApiUrl, [
                'headers' => $authHeader,
                'json' => $emailData,
            ]);

            if ($response->getStatusCode() === 200) {
                return 'Email sent successfully!';
            } else {
                return 'Failed to send email.';
                \Log::error('sendGoalsEmail Error: ' . $response);
            }
        } catch (\Exception $e) {
            \Log::error('sendGoalsEmail Error: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }


    /*
    // TODO
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            // Call your method to send emails
            (new ZohoController)->sendEmail(new Request(['to' => 'example@example.com', 'subject' => 'Test', 'content' => 'Hello World']));
        })->daily(); // Adjust the frequency as needed
    }
    */

    public function sendEmail()
    {

        // Get the Zoho OAuth token through your own authentication process
        $accessToken = ZohoController::getZohoAccessToken();

        if(!$accessToken){
            return 'Failed to accessToken.';
        }

        // Make an authenticated API request to fetch account details
        $accountData = ZohoController::getZohoAccountData($accessToken);

        if (!$accountData) {
            \Log::error('Failed to fetch Zoho account data');

            return 'Failed to fetch Zoho account data.';
        }

        // Extract the account_id from the response
        $accountId = $accountData['data'][0]['account_id'];

        $zohoApiUrl = "https://mail.zoho.com/api/accounts/{$accountId}/messages";
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');

        $client = new Client();

        $authHeader = [
            'Authorization' => "Zoho-oauthtoken {$clientId}:{$clientSecret}",
        ];

        // Define your email data here
        $emailData = [
            'fromAddress' => env('ZOHO_CURRENT_USER_EMAIL'),
            'toAddress' => 'bertogross@gmail.com',
            'subject' => 'Test Email',
            'content' => 'Your test email content here.',
        ];

        try {
            $response = $client->post($zohoApiUrl, [
                'headers' => $authHeader,
                'json' => $emailData,
            ]);

            if ($response->getStatusCode() === 200) {
                return 'Email sent successfully!';
            } else {
                return 'Failed to send email.';
                \Log::error('sendGoalsEmail Error: ' . $response);
            }
        } catch (\Exception $e) {
            \Log::error('sendGoalsEmail Error: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }

}

