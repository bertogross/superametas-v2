<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UploadController extends Controller
{
    // Define the custom database connection name
    protected $connection = 'smAppTemplate';

    // Handle avatar upload
    public function uploadAvatar(Request $request)
    {
        // Delegate the upload process to the generic uploadFile method
        return $this->uploadFile($request, 'avatar', 'avatars');
    }

    // Handle cover image upload
    public function uploadCover(Request $request)
    {
        // Delegate the upload process to the generic uploadFile method
        return $this->uploadFile($request, 'cover', 'covers');
    }

    /**
     * Generic method to handle file uploads.
     *
     * @param Request $request The incoming request object
     * @param string $type The type of file being uploaded (avatar or cover)
     * @param string $folder The storage folder for the uploaded file
     * @return \Illuminate\Http\Response
     */
    private function uploadFile(Request $request, $type, $folder)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'file' => 'required|file|mimes:jpeg,jpg|max:5120',
                'user_id' => 'required|integer|exists:smAppTemplate.users,id'
            ]);

            // Extract the user ID from the request
            $userID = intval($request->input('user_id'));

            // Retrieve the user from the database using the custom connection
            $user = User::on($this->connection)->find($userID);

            // Check if the user exists
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            // Check if a file was provided in the request
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                // Get the database name from the connection configuration
                $config = config("database.connections.{$this->connection}");
                $dbName = $config['database'];

                $path = "{$dbName}/" . date('Y') . '/' . date('m') . '/' . $folder;

                $filePath = $file->store($path, 'public');

                // Delete old avatar
                if ($type === 'avatar' && $user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                    $user->avatar = $filePath;
                }

                // Delete old cover image
                elseif ($type === 'cover' && $user->cover) {
                    Storage::disk('public')->delete($user->cover);
                    $user->cover = $filePath;
                }

                // Save the updated user data to the database
                $user->save();

                return response()->json(['success' => true, 'message' => ucfirst($type) . ' uploaded successfully!', 'path' => $filePath], 200);
            }

            return response()->json(['success' => false, 'message' => 'File not provided'], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadLogo(Request $request)
    {
        try {
            // Validate the uploaded file
            $request->validate([
                'logo' => 'required|file|mimes:jpeg,jpg|max:5120', // Only allow JPEG images up to 5MB
            ]);

            // Check if there's an old logo and delete it
            $oldLogo = DB::connection($this->connection)->table('settings')->where('key', 'logo')->value('value');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Get the database name from the connection configuration
            $config = config("database.connections.{$this->connection}");
            $dbName = $config['database'];

            $path = "{$dbName}/" . date('Y') . '/' . date('m') . '/logo';

            // Store the uploaded file
            $file = $request->file('logo');
            $filePath = $file->store($path, 'public');

            // Save the file path in the settings table
            $settings = DB::connection($this->connection)->table('settings')->updateOrInsert(
                ['key' => 'logo'],
                ['value' => $filePath]
            );

            return response()->json(['success' => true, 'message' => 'Logo uploaded successfully!', 'path' => $filePath], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteLogo(Request $request)
    {
        try {
            // Retrieve the logo path from the settings table
            $logoPath = DB::connection($this->connection)->table('settings')->where('key', 'logo')->value('value');

            // Check if the logo exists and delete it
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);

                // Optionally, you can also remove the logo path from the settings table
                DB::connection($this->connection)->table('settings')->where('key', 'logo')->delete();

                return response()->json(['success' => true, 'message' => 'Logo deleted successfully!'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Logo not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
