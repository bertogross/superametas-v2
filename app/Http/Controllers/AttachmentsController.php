<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Attachments;

class AttachmentsController extends Controller
{
    protected $connection = 'smAppTemplate';

    public function uploadPhoto(Request $request)
    {
        $folder = 'attachments';

        try {
            // Validate the incoming request data
            $request->validate([
                'file' => 'required|file|mimes:jpeg,jpg|max:5120',
            ]);

            $currentUserId = auth()->id();

            // Check if a file was provided in the request
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $fileExtension = $file->getClientOriginalExtension();

                // Get the database name from the connection configuration
                $config = config("database.connections.{$this->connection}");
                $dbName = $config['database'];

                $path = "{$dbName}/" . $folder . '/' . date('Y') . '/' . date('m');

                $filePath = $file->store($path, 'public');

                // Inside uploadFile method
                $attachment = new Attachments();
                $attachment->user_id = $currentUserId;
                $attachment->path = $filePath;
                $attachment->type = $fileExtension;
                // $attachment->title = '...';
                // $attachment->description = '...';
                // $attachment->size = $file->getSize();
                // $attachment->order = ...;

                // Save the updated user data to the database
                $attachment->save();

                return response()->json(['success' => true, 'message' => 'Arquivo armazenado!', 'path' => $filePath, 'id' => $attachment->id], 200);
            }

            return response()->json(['success' => false, 'message' => 'Arquivo não fornecido'], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deletePhoto(Request $request, $id)
    {
        try {

            // Retrieve the attachment from the database
            $attachment = Attachments::find($id);

            if (!$attachment) {
                return response()->json(['success' => false, 'message' => 'Anexo não encontrado'], 404);
            }

            // Delete the file from storage
            if (Storage::disk('public')->exists($attachment->path)) {
                Storage::disk('public')->delete($attachment->path);
            }

            // Delete the attachment record from the database
            $attachment->delete();

            return response()->json(['success' => true, 'message' => 'Anexo excluído com êxito'], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


}
