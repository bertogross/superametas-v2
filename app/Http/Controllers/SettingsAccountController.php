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
            'name.required' => 'The company name is required.',
            'name.max' => 'The company name may not be greater than 191 characters.',
            'phone.required' => 'The phone number is required.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email may not be greater than 100 characters.',
            'new_password.min' => 'The password must be at least 8 characters.',
            'new_password.max' => 'The password may not be greater than 8 characters.',
        ];

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'required|string|max:20',
            //'email' => 'required|email|max:100',
            'new_password' => 'nullable|string|min:8|max:8',
        ], $messages);

        // Update or insert other settings
        $this->updateOrInsertSetting('name', $request->name);

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
