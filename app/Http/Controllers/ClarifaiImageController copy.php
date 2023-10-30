<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class ClarifaiImageController extends Controller
{
    protected $connection = 'smAppTemplate';

    public function index()
    {
        return view('audits.clarifai.submit');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,jpg|max:5120',
        ]);

        // Get the database name from the connection configuration
        $config = config("database.connections.{$this->connection}");
        $dbName = $config['database'];

        $folder = 'clarify';

        $path = "{$dbName}/" . date('Y') . '/' . date('m') . '/' . $folder;

        // Ensure the directory exists
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('image');

            $text = $request->input('text');

            $filePath = $file->store($path, 'public');
            if (!$filePath) {
                return response()->json(['message' => 'Failed to store file'], 500);
            }

            // Check if the file exists and is readable
            $fullPath = storage_path('app/public/' . $filePath);
            if (!file_exists($fullPath) || !is_readable($fullPath)) {
                return response()->json(['message' => 'File does not exist or is not readable', 'path' => $fullPath], 500);
            }

            // Call the analyze method to process the image
            $results = $this->analyze($filePath, $text);

            // Return the results as part of the JSON response
            return response()->json([
                'message' => 'Image submitted successfully.',
                'results' => $results,
            ]);
        }
        return response()->json(['success' => false, 'message' => 'File not provided'], 422);

    }


    public function analyze($path, $text)
    {
        $imagePath = Storage::disk('public')->path($path);

        if (!file_exists($imagePath)) {
            return response()->json(['message' => 'File does not exist'], 404);
        }

        if (!is_readable($imagePath)) {
            return response()->json(['message' => 'File is not readable'], 500);
        }

        // Call the Clarifai cloud-based API
        $apiKey = 'dcd036f7c828413d92f92e16fbb302b4';
        $modelId = 'Store-Visual-Analytics-V1';
        $url = 'https://api.clarifai.com/v2/models/general/outputs';

        $client = new Client(['verify' => false]);
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'Key ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'inputs' => [
                    [
                        'data' => [
                            'image' => [
                                'base64' => base64_encode(file_get_contents($imagePath)),
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $result = json_decode($response->getBody(), true);
        $problems = $result['outputs'][0]['data']['concepts'];

        return $results;
    }
}
