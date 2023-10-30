<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ScenexImageController extends Controller
{
    protected $connection = 'smAppTemplate';

    protected $pat = '6140cf7a828c4eddaaf46ed2bdb5bea0';
    protected $userId = 'clarifai';
    protected $appId = 'main';
    protected $modelId = 'general-image-recognition';
    protected $modelVersionId = 'aa7f35c01e0642fda5cf400f543e7c40';

    public function submit(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,jpg|max:5120',
            ]);

            if ($request->hasFile('file')) {
                $result = '';

                $file = $request->file('file');
                $text = $request->input('text');

                if (!$file) {
                    return response()->json(['message' => 'Please select an image.'], 400);
                }

                if (!$text) {
                    return response()->json(['message' => 'Please enter some text.'], 400);
                }
                // \Log::info("File object: " . print_r($file, true));  // Log the file object for debugging

                // Get the database name from the connection configuration
                $config = config("database.connections.{$this->connection}");
                $dbName = $config['database'];
                $folder = 'clarifai';

                $path = "{$dbName}/" . date('Y') . '/' . date('m') . '/' . $folder;

                // Ensure the directory exists
                if (!Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->makeDirectory($path);
                }

                $filePath = $file->store($path, 'public');
                $absoluteFilePath = Storage::disk('public')->path($filePath);

                if (!file_exists($absoluteFilePath)) {
                    \Log::error("File does not exist at path: $absoluteFilePath");
                    return response()->json(['message' => 'File does not exist.'], 400);
                }

                $base64Image = base64_encode(file_get_contents($absoluteFilePath));

                $client = new Client();
                $response = $client->post('https://api.clarifai.com/v2/models/' . $this->modelId . '/versions/' . $this->modelVersionId . '/outputs', [
                    'headers' => [
                        'Authorization' => 'Key ' . $this->pat,
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'user_app_id' => [
                            'user_id' => $this->userId,
                            'app_id' => $this->appId,
                        ],
                        'inputs' => [
                            [
                                'data' => [
                                    'image' => [
                                        'base64' => $base64Image,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);

                $result = json_decode($response->getBody(), true);

                return response()->json([
                    'message' => 'Image submitted successfully.',
                    'results' => $result,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'File not provided'], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
