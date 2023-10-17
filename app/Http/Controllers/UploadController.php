<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    protected $connection = 'smAppTemplate';

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg|max:5120', // Validate the file
        ]);

        if($request->hasFile('filepond')) {
            $file = $request->file('filepond');

            // Assume you have CLIENT_ID from user or request
            $clientId = auth()->user()->client_id; // Example, adapt as needed

            // Construct the path
            $path = 'uploads/' . $clientId . '/' . now()->format('Y/m');

            // Store the file and get its path
            $filePath = $file->store($path, 'public');

            // TODO logic here (save path to database table attachment)

            return response()->json(['path' => $filePath], 200);
        }

        return response()->json(['error' => 'File not provided'], 422);
    }
}
