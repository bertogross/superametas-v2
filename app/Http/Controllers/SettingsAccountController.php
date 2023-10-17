<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SettingsAccountController extends Controller
{
    protected $connection = 'smAppTemplate';

    public function show()
    {
        $settings = DB::connection($this->connection)->table('settings')->pluck('value', 'key')->toArray();
        return view('settings-account', compact('settings'));
    }

    public function store(Request $request)
    {
        \Log::info($request->all());

        // Custom error messages
        $messages = [
            'company_name.required' => 'The company name is required.',
            'company_name.max' => 'The company name may not be greater than 191 characters.',
            'phone.required' => 'The phone number is required.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email may not be greater than 100 characters.',
            'new_password.min' => 'The password must be at least 8 characters.',
            'new_password.max' => 'The password may not be greater than 8 characters.',
            'company_logo.image' => 'The file must be an image.',
            'company_logo.mimes' => 'The image must be a type of: jpeg, jpg.',
            'company_logo.max' => 'The image may not be greater than 5120 kilobytes.',
        ];

        // Validate the request data
        $request->validate([
            'company_name' => 'required|string|max:191',
            'phone' => 'required|string|max:20',
            //'email' => 'required|email|max:100',
            'new_password' => 'nullable|string|min:8|max:8',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg|max:5120',
        ], $messages);

        // Update user email and password if provided
        /*
        $user = auth()->user();
        $user->email = $request->email;
        if ($request->new_password) {
            $user->password = Hash::make($request->new_password);
        }
        $user->save();
        */

        // Handle file upload
        if($request->hasFile('company_logo')) {
            $image = $request->file('company_logo');
            $filename = 'logo-' . date('YmdHis') . '.' . $image->getClientOriginalExtension();

            // Ensure the directory exists
            $uploadDir = 'uploads/' . auth()->id() . '/images';
            if (!Storage::disk('public')->exists($uploadDir)) {
                Storage::disk('public')->makeDirectory($uploadDir);
            }

            // Store the file
            $path = $image->storeAs($uploadDir, $filename, 'public');

            // Only update the setting if the file is successfully uploaded
            if($path) {
                $this->updateOrInsertSetting('company_logo', $path);
            } else {
                return redirect()->back()->with('error', 'Failed to upload the logo. Please try again.');
            }
        }

        // Update or insert other settings
        $this->updateOrInsertSetting('company_name', $request->company_name);

        // Remove non-numeric characters from phone number
        $cleanedPhone = preg_replace('/\D/', '', $request->phone);
        $this->updateOrInsertSetting('phone', $cleanedPhone);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Update or insert a setting.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    protected function updateOrInsertSetting(string $key, $value)
    {
        DB::connection($this->connection)->table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value]
        );
    }

}
