<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

//use Illuminate\Support\Facades\Log;

class SettingsAccountController extends Controller
{
    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    public function index()
    {
        return view('settings.index');
    }

    public function show()
    {
        $settings = DB::connection('smAppTemplate')->table('settings')->pluck('value', 'key')->toArray();

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        $getStripeData = getStripeData();
        $customerId = $getStripeData['customer_id'] ?? '';
        $subscriptionId = $getStripeData['subscription_id'] ?? '';

        $subscriptionItemId = '';

        return view('settings.account', compact(
                'settings',
                'stripe',
                'customerId',
                'subscriptionId',
                'subscriptionItemId'
            )
        );
    }

    public function storeOrUpdate(Request $request)
    {
        //\Log::info($request->all());

        // Custom error messages
        $messages = [
            'name.required' => 'O nome da empresa é obrigatório.',
            'name.max' => 'O nome da empresa não pode ter mais de 191 caracteres.',
            'user_name.required' => 'Seu nome é obrigatório.',
            'user_name.max' => 'Seu nome não pode ter mais de 191 caracteres.',
            'phone.required' => 'O número de telefone é obrigatório.',
            'phone.max' => 'O número de telefone não pode ter mais de 16 caracteres.',
        ];

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:191',
            'user_name' => 'required|string|max:191',
            'phone' => 'required|string|max:16',
        ], $messages);

        $this->updateOrInsertSetting('name', $request->name);

        $this->updateOrInsertSetting('user_name', $request->user_name);

        // Remove non-numeric characters from phone number
        $cleanedPhone = onlyNumber($request->phone);
        $this->updateOrInsertSetting('phone', $cleanedPhone);

        return redirect()->back()->with('success', 'Configurações atualizadas com êxito!');
    }

    /**
     * Update or insert a setting.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function updateOrInsertSetting(string $key, $value)
    {
        DB::connection('smAppTemplate')->table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value]
        );
    }


}
