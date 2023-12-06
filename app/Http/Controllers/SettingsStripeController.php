<?php
namespace App\Http\Controllers;

use Exception;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SettingsStripeController extends Controller
{
    protected $connection = 'smAppTemplate';

    protected $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
        $this->stripe->setApiKey(config('services.stripe.secret'));
    }

    /*
    public function charge(Request $request)
    {
        $validated = $request->validate([
            'stripeToken' => 'required',
            // other validations as needed
        ]);

        try {
            $charge = $this->stripe->charges->create([
                'amount' => 1000, // $10 in cents
                'currency' => 'usd',
                'source' => $validated['stripeToken'],
                'description' => 'Test charge',
            ]);

            return back()->with('success_message', 'You have been successfully charged!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Charge failed: ' . $e->getMessage()]);
        }
    }
    */

    public function subscriptionStatus($checkout)
    {
        if (!$this->stripe) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Falha na conexão com a Stripe'
            ], 500); // 500 Internal Server Error
        }

        $customerId = $checkout->customer;

        $subscriptionId = $checkout->subscription;

        // Cancel other subscriptions
        $this->stripeSubscriptionCancelOthers($subscriptionId, $customerId);

        if (!empty($subscriptionId)) {
            try {
                $retrieveSubscription = $this->stripe->subscriptions->retrieve(
                    $subscriptionId,
                    []
                );

                $subscriptionStatus = !empty($retrieveSubscription->status) ? $retrieveSubscription->status : 'trialing';
                $quantity = isset($retrieveSubscription->quantity) ? intval($retrieveSubscription->quantity) : 0;

                // Update the database
                $result = DB::connection('smOnboard')->table('app_users')
                    ->where('user_stripe_customer_id', $customerId)
                    ->update([
                        'user_stripe_subscription_id' => $subscriptionId,
                        'user_stripe_subscription_status' => $subscriptionStatus,
                        'user_stripe_subscription_quantity' => $quantity
                    ]);

                if (!$result) {
                    // Log error and return response
                    \Log::error('Falha na atualização do banco de dados', [
                        'method' => __METHOD__,
                        'path' => __FILE__
                    ]);

                    return response()->json([
                        'success' => false,
                        'title' => 'Erro',
                        'message' => 'Falha na atualização do banco de dados'
                    ], 500); // 500 Internal Server Error
                }
            } catch (Exception $e) {
                // Log error and return response
                \Log::error($e->getMessage(), [
                    'method' => __METHOD__,
                    'path' => __FILE__,
                    'stripeCode' => $e->getCode()
                ]);

                return response()->json([
                    'success' => false,
                    'title' => 'Erro',
                    'message' => $e->getMessage()
                ], 500); // 500 Internal Server Error
            }
        }

        return response()->json(['success' => true], 200); // 200 OK
    }

    public function subscriptionStatusChange(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Você precisa efetuar login'
            ]);
        }

        // Assuming getStripeData() is a method that retrieves Stripe data
        $stripeData = getStripeData();
        $subscriptionId = $stripeData['subscription_id'];
        $actualSubscriptionStatus = $request->input('subscription_status');
        $subscriptionItems = $request->input('subscription_items', []);

        try {
            if ($actualSubscriptionStatus == 'active') {
                $this->handleActiveSubscription($subscriptionItems, $subscriptionId);
            } else {
                $this->handleInactiveSubscription($subscriptionId);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Error updating subscription: ' . $e->getMessage()
            ]);
        }
    }

    public function getOrCreateStripeCustomer()
    {
        $user = Auth::user(); // Assuming you have user authentication set up

        if (!$user) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Usuário não autenticado'
            ]);
        }

        $displayName = $user->name; // Replace with your user model's appropriate field
        $userEmail = $user->email; // Replace with your user model's appropriate field

        try {
            // Find customer ID
            $customers = $this->stripe->customers->search([
                'query' => "email:'{$userEmail}'",
            ]);

            $customerId = '';
            foreach ($customers->data as $customer) {
                $customerId = $customer->id;
                break;
            }

            // If customer doesn't exist, create
            if (empty($customerId)) {
                $customer = $this->stripe->customers->create([
                    'name' => $displayName,
                    'email' => $userEmail,
                    'preferred_locales' => ['pt-BR'],
                    'metadata' => ['onboard_id' => $user->id] // Assuming onboard_id is the user's ID
                ]);

                $customerId = $customer->id;

                // Update user's Stripe customer ID in the database
                $user->stripe_customer_id = $customerId;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'customer_id' => $customerId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Erro ao criar ou recuperar cliente Stripe: ' . $e->getMessage()
            ]);
        }
    }

    public function createStripeSession(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Falha na conexão com a Stripe: ' . $e->getMessage()
            ]);
        }

        // Assuming you have a method to get or create a customer
        $customerId = $this->getOrCreateStripeCustomer();

        $stripeData = getStripeData();
        $subscriptionId = $stripeData['subscription_id'];

        if (!empty($subscriptionId)) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Você deverá primeiramente cancelar a assinatura vigente.'
            ]);
        }

        $priceId = $request->input('price_id', '');
        $quantity = max(1, (int) $request->input('quantity', 1));

        try {
            $checkoutSession = $stripe->checkout->sessions->create([
                'customer' => $customerId,
                'client_reference_id' => 'YourClientReferenceId', // Replace with actual reference ID
                'success_url' => url('/settings?section=account'), // Adjust URL as needed
                'cancel_url' => url('/settings?section=account'), // Adjust URL as needed
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => $quantity,
                    ],
                ],
                'mode' => 'subscription',
                'allow_promotion_codes' => true,
            ]);

            return response()->json(['success' => true, 'stripe' => $checkoutSession]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Erro ao criar sessão Stripe: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelStripeSubscription(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Falha na conexão com a Stripe: ' . $e->getMessage()
            ]);
        }

        // Assuming you have a method to get the customer ID
        $customerId = $this->getCustomerId();

        if (empty($customerId)) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Cliente não encontrado'
            ]);
        }

        try {
            $subscriptionItems = $stripe->subscriptions->all([
                'customer' => $customerId
            ]);

            foreach ($subscriptionItems->data as $subscription) {
                $stripe->subscriptions->cancel(
                    $subscription->id,
                    ['prorate' => true]
                );
            }

            return response()->json([
                'success' => true,
                'title' => 'Inativo',
                'message' => 'A assinatura foi cancelada'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Erro ao cancelar assinatura: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelOtherStripeSubscriptions(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Falha na conexão com a Stripe: ' . $e->getMessage()
            ]);
        }

        $currentSubscriptionId = $request->input('current_subscription_id');
        $customerId = $request->input('customer_id');

        if (empty($customerId) || empty($currentSubscriptionId)) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Informações de assinatura ausentes'
            ]);
        }

        try {
            $subscriptionItems = $stripe->subscriptions->all([
                'customer' => $customerId
            ]);

            foreach ($subscriptionItems->data as $subscription) {
                if ($subscription->id != $currentSubscriptionId) {
                    $stripe->subscriptions->cancel(
                        $subscription->id,
                        ['prorate' => true]
                    );
                }
            }

            // TODO: Implement Step 1 - Calculate penalty on cancellation if necessary

            return response()->json([
                'success' => true,
                'title' => 'Sucesso',
                'message' => 'Outras assinaturas canceladas'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Erro ao cancelar outras assinaturas: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteSubscriptionItem(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $itemId = $request->input('item_ID', '');
        $addonName = $request->input('addon_name', '');

        if (empty($itemId)) {
            return response()->json([
                'success' => false,
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'ID do item não fornecido'
            ]);
        }

        try {
            $stripe->subscriptionItems->delete($itemId, []);

            // Optionally, update the user's record in your database here
            // This can be done via a webhook or directly if needed

            return response()->json([
                'success' => true,
                'type' => 'success',
                'title' => $addonName,
                'message' => 'O Addon foi desativado'
            ]);
        } catch (Exception $e) {
            // Log the error or handle it as needed
            return response()->json([
                'success' => false,
                'type' => 'error',
                'title' => 'Erro',
                'message' => 'Algo errado ocorreu e Addon ' . $addonName . ' não foi desativado. Por favor, atualize a página e tente novamente.'
            ]);
        }
    }

    public function updateSubscriptionItem(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        // Assuming you have a middleware to check for user login
        // and a method to get onboard data
        $stripeData = getStripeData();
        $subscriptionId = $stripeData['subscription_id'];
        $subscriptionStatus = $stripeData['subscription_status'];

        $priceId = $request->input('price_id', '');
        $subscriptionItemId = $request->input('subscription_item_id', '');
        $productQuantity = $request->input('quantity', 0);

        if ($subscriptionStatus != 'active') {
            $message = '<p>Para prosseguir você deverá primeiramente ativar sua assinatura</p>';

            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => $message
            ]);
        }

        if (!empty($subscriptionItemId) && $productQuantity > 0) {
            try {
                $stripe->subscriptionItems->update(
                    $subscriptionItemId,
                    ['price' => $priceId, 'quantity' => $productQuantity]
                );

                //And, update app_users table via webhook case 'customer.subscription.updated'

                return response()->json(['success' => true, 'title' => 'Assinatura Atualizada']);
            } catch (Exception $e) {
                // Log the error or handle it as needed
                return response()->json([
                    'success' => false,
                    'title' => 'Erro',
                    'message' => 'Algo errado ocorreu. Por favor, atualize a página e tente novamente. ' . $e->getMessage()
                ]);
            }
        }
    }

    public function updateStripeCustomer(Request $request)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Você precisa efetuar login'
            ]);
        }

        $user = Auth::user(); // Get the authenticated user

        // Assuming the user model has a stripe_customer_id field
        $customerId = $user->stripe_customer_id;

        if (!$customerId) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Cliente Stripe não encontrado'
            ]);
        }

        try {
            // Retrieve the Stripe customer
            $customer = $this->stripe->customers->retrieve($customerId);

            // Update the customer's details
            // Replace 'field' with actual fields you want to update
            $updatedCustomer = $this->stripe->customers->update($customerId, [
                'field' => $request->input('field'),
                // Add other fields as needed
            ]);

            return response()->json([
                'success' => true,
                'customer' => $updatedCustomer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Erro ao atualizar cliente Stripe: ' . $e->getMessage()
            ]);
        }
    }

    public function getRelatedItemIdFromPriceId(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        // Assuming you have a middleware to check for user login
        $itemIds = $request->input('item_IDs', []);
        $priceId = $request->input('price_id', '');

        if (!is_array($itemIds) || count($itemIds) === 0) {
            return response()->json(['success' => false, 'title' => 'Erro', 'message' => 'Invalid item IDs']);
        }

        foreach ($itemIds as $id) {
            try {
                $subscriptionItem = $stripe->subscriptionItems->retrieve($id, []);

                if ($subscriptionItem->price->id == $priceId) {
                    return response()->json(['success' => true, 'item_id' => $id]);
                }
            } catch (Exception $e) {
                // Log the error or handle it as needed
                // Continue to the next item in case of an exception
                continue;
            }
        }

        return response()->json(['success' => false, 'title' => 'Erro', 'message' => 'Related item ID not found']);
    }

    public function retrieveProductFromPriceId(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $priceId = $request->input('price_id', '');

        if (empty($priceId)) {
            return response()->json(['success' => false, 'title' => 'Erro', 'message' => 'Price ID is required']);
        }

        try {
            $price = $stripe->prices->retrieve($priceId, ['expand' => ['product']]);
            return response()->json(['success' => true, 'product' => $price]);
        } catch (Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['success' => false, 'title' => 'Erro', 'message' => 'Failed to retrieve product']);
        }
    }

    public function retrieveProduct(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $productId = $request->input('product_ID', '');

        if (empty($productId)) {
            return response()->json(['success' => false, 'title' => 'Erro', 'message' => 'Product ID is required']);
        }

        try {
            $product = $stripe->products->retrieve($productId);
            return response()->json(['success' => true, 'product' => $product]);
        } catch (Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['success' => false, 'title' => 'Erro', 'message' => 'Failed to retrieve product']);
        }
    }

    public function retrieveStripeCustomer($customerId = null)
    {
        if (!$customerId) {
            $user = Auth::user(); // Assuming you have user authentication set up
            $customerId = $user->stripe_customer_id; // Replace with your user model's appropriate field

            if (!$customerId) {
                return response()->json([
                    'success' => false,
                    'title' => 'Erro',
                    'message' => 'Customer ID is missing'
                ]);
            }
        }

        try {
            $customer = $this->stripe->customers->retrieve($customerId);

            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Error retrieving customer: ' . $e->getMessage()
            ]);
        }
    }

    public function handleActiveSubscription($subscriptionItems, $subscriptionId)
    {
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Falha na conexão com a Stripe: ' . $e->getMessage()
            ]);
        }

        if( !empty($subscriptionItems) && is_array($subscriptionItems) && count($subscriptionItems) > 0 ){
            foreach( $subscriptionItems as $itemId ){
              //https://stripe.com/docs/api/subscription_items/delete
              $stripe->subscriptionItems->delete(
                $itemId,
                []
              );
            }
          }

          try {
            /**
             * Pause subscription
             * https://stripe.com/docs/billing/subscriptions/pause
             */
            $stripe->subscriptions->update($subscriptionId, [ 'pause_collection' => ['behavior' => 'mark_uncollectible'] ]);

            return response()->json([
                'success' => true,
                'message' => 'A assinatura foi suspensa'
            ]);
          }catch (Exception $e){
            //https://www.php.net/manual/pt_BR/language.exceptions.php
            //https://stripe.com/docs/api/errors/handling
            if($e){
                return response()->json([
                    'success' => false,
                    'title' => 'Erro',
                    'message' => 'Error updating subscription: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function handleInactiveSubscription($subscriptionId)
    {
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'title' => 'Erro',
                'message' => 'Falha na conexão com a Stripe: ' . $e->getMessage()
            ]);
        }

        try {
            //Temporarily offer services for free and collect payment later
            //https://stripe.com/docs/billing/subscriptions/pause#collect-payment-later
            $stripe->subscriptions->update($subscriptionId,[ 'pause_collection' => '' ]);

            return response()->json([
                'success' => true,
                'message' => 'A assinatura foi reativada'
            ]);

          }catch (Exception $e){
            //https://www.php.net/manual/pt_BR/language.exceptions.php
            //https://stripe.com/docs/api/errors/handling
            if($e){
                return response()->json([
                    'success' => false,
                    'title' => 'Erro',
                    'message' => 'Error updating subscription: ' . $e->getMessage()
                ]);
            }
        }
    }

    protected function handleWebhookSubscriptionDeleted($subscription)
    {
        $subscriptionId = isset($subscription->id) ? $subscription->id : '';

        $customerId = isset($subscription->customer) ? $subscription->customer : '';

        $subscriptionStatus = isset($subscription->status) ? $subscription->status : 'active';

        if( !empty($customerId) && !empty($subscriptionId) ){

            $result = DB::connection('smOnboard')->table('app_users')
            ->where('user_stripe_customer_id', $customerId)
            ->update([
                'user_stripe_subscription_id' => $subscriptionId,
                'user_stripe_subscription_status' => $subscriptionStatus,
                'user_stripe_subscription_quantity' => $quantity,
                'user_stripe_products' => ''
            ]);

            if(!$result){
                http_response_code(302);

                exit();
            }
        }
    }

    protected function handleWebhookSubscriptionUpdated($subscription)
    {
        $productIds = array();

        $subscriptionId = $subscription->id;

        $customerId = $subscription->customer;

        $subscriptionStatus = !empty($subscription->pause_collection['behavior']) ? $subscription->pause_collection['behavior'] : '';

        $quantity = isset($subscription->quantity) ? intval($subscription->quantity) : 0;

        $subscriptionStatus = !empty($subscriptionStatus) && $subscriptionStatus == 'mark_uncollectible' ? 'uncollectible' : 'active';


        /*************************
         * Start Extract Product IDs
         * https://stripe.com/docs/api/subscriptions/retrieve
         *************************/
        $subscriptions = $stripe->customers->retrieve(
            $customerId,
            [ 'expand' => ['subscriptions'] ]
        );
        $subscriptionsItems = $subscriptions->subscriptions->data[0]->items->data;

        if( !empty($subscriptionsItems) && is_array($subscriptionsItems) && count($subscriptionsItems) > 0 ){
            foreach($subscriptionsItems as $key => $item){
            $productIds[] = array(
                'item' => isset($item->id) ? $item->id : '',
                'price' => isset($item->price->id) ? $item->price->id : '',
                'product' => isset($item->price->product) ? $item->price->product : '',
            );

            }
        }

        $productIds = array_filter($productIds);

        $productIds = is_array($productIds) && count($productIds) > 0 ? serialize($productIds) : '';
        /**************************
         * End Extract Product IDs
         *************************/


        if( !empty($customerId) && !empty($subscriptionId) ){

            $result = DB::connection('smOnboard')->table('app_users')
            ->where('user_stripe_customer_id', $customerId)
            ->update([
                'user_stripe_subscription_id' => $subscriptionId,
                'user_stripe_subscription_status' => $subscriptionStatus,
                'user_stripe_subscription_quantity' => $quantity,
                'user_stripe_products' => $productIds
            ]);

            if(!$result){
                http_response_code(302);

                exit();
            }
        }
    }

    protected function getCartDetails(array $cart)
    {
        $details = [];
        foreach ($cart as $priceId) {
            try {
                $price = $this->stripe->prices->retrieve($priceId);
                $product = $this->stripe->products->retrieve($price->product);
                $details[] = [
                    'productName' => $product->name,
                    'price' => $price->unit_amount,
                    // other details you need
                ];
            } catch (\Exception $e) {
                // Handle exceptions
            }
        }
        return $details;
    }

    public function addonCart(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $onboardData = $this->getOnboardData(); // Assuming this is a method to fetch onboard data
        $subscriptionStatus = $onboardData['user']['subscription_status'];

        if ($subscriptionStatus != 'active') {
            return response()->json(['error' => 'Subscription not active'], 403);
        }

        $cart = $request->input('cart', []);
        $cartDetails = $this->getCartDetails($cart);

        return response()->json(['cartDetails' => $cartDetails]);
    }

    public static function subscriptionStatusTranslation($status)
    {
        //https://stripe.com/docs/api/subscriptions/object
        //Possible values are incomplete, incomplete_expired, trialing, active, past_due, canceled, or unpaid. The 'uncollectible' is a custom value returned when mark_uncollectible

        switch ($status) {
            case 'trialing':
            case 'incomplete':
            case 'incomplete_expired':
                $label = 'versão demonstrativa';
                $description = 'Esta versão permite a inserção de alguns registros com a finalidade de que você habitue-se com a aplicação. <br><br> Para mais recursos será necessário assinar o '.env('APP_NAME').'.';
                $color = 'warning';
                $class = '';
                break;
            case 'active':
                $label = 'Assinatura Ativa';
                $description = 'Este é um indicador de que a assinatura está financeiramente em dia.';
                $color = 'theme';
                $class = '';
            break;
            case 'past_due':
            case 'unpaid':
                $label = 'Assinatura requer atenção';
                $description = 'Este indicador sugere que você verifique e atualize o método de pagamento.';
                $color = 'danger';
                $class = 'blink';
                break;
            case 'canceled':
                $label = 'Assinatura Cancelada';
                $description = 'Este é um indicador de que a assinatura foi cancelada.';
                $color = 'danger';
                $class = '';
                break;
            case 'uncollectible'://this ia s custom value, not stripe
                $label = 'Assinatura Suspensa';
                $description = 'Este é um indicador de que você solicitou a suspensão da assinatura.';
                $color = 'warning';
                $class = '';
                break;
            default:
                $label = '';
                $description = '';
                $color = '';
                $class = '';
        }

        $description .= "<br><br><small>*Etiqueta exibida somente ao Administrativo</small>";

        return array('label' => $label, 'description' => $description, 'color' => $color, 'class' => $class);
    }


}
