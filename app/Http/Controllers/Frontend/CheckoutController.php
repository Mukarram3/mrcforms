<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Backend\Payments\PaymentsController;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\City;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\LogisticZone;
use App\Models\LogisticZoneCity;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\ProductPrice;
use App\Models\ScheduledDeliveryTimeList;
use App\Models\UserAddress;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

//use Notification;

class CheckoutController extends Controller
{
    private $publicPEMKey = "-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAuXTAlQEapn9KKhVekNyG
VpFLrpwcYRMHRSeR031K1fXeWs91MmqwpdM1qe6SIyhnzFc9p3mQ7eKmC7gmTgEb
18/UcBkSfzKm6ZEZcnVgIwS94Z8+dIyiAXVeQ1ti698h05ETtfJ1Xy4DTWhusCAF
KeJ0owzUxUcYoE25GmPv92EeQFyZ/CfAcEsZbfSIdPTRxwTgYYEhP5ugW3JdmQmr
DEOMB1a+NH1ejjzASnEZnrdQIVcAMPzcqe/dvhTWqhWASysom4uuOXHTm3sA1A27
B0KO7+mFOketMq9lyXeeqH0UqdtxB8sLf2Vm7CyTqTCJyCSgJYk5GsM14pm8puOT
SJTowB8Rb6JlFgveiWTdi6nJ2oCxvQcgU3TNxaT6wJ8giweAlLTTn5k+x2DS92bI
zjoC7zFK31r4NqBAz5z183TMu+mKBwL3/D/KS+xcN2MF71XAJWs8BJacLsTaSEQu
i3j6wN/mVWzM7Ik18WsVGf+zMHmFG8suQwmUtneE/zqQiUfO2DWB8Rmj3e9YPR01
1tzU65XxpJXcbEjEoIYXPXq58wMbAg1oGfpA3fiBKIdZQMgD4C4pSlbImCoNeKDj
nhmj8MLr9OSMsjCxhyHUBKPe750kMafgnctxqHePpw79rso6y0XNCM7dFGFdINMD
EKTvvzyYfz8EtO/X37zznIkCAwEAAQ==
-----END PUBLIC KEY-----";

//    private $publicPEMKey = "-----BEGIN PUBLIC KEY-----
//MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA4ObKYoTVE7gHhN1zpX7I
//8IhGWoBPdMt6nQO3nYxRx2ijredacq7oRsPxzNs5IXYwS/rJRsrVDS/ZqMfNtNVF
//rz0tN1XVqb2QFE+661xBhnL98FqkXY3Piri5G7bmUye4h8iCfZEvl6ahbhBL95T7
//OU05gYRlw+3QQtwD+cfUNGWsPiYuLtJvjKmtSRd8Lkkycg6k4960MSfGo93rl0cJ
//EOBpWJyIdDN4pxblRBysxwqChjhUvO5YsKat5CLkSPfJtDfAvQInJs86ieu4uJV6
//A47VXkTOyIYek/ysU0Ln86M/hR1QKR4AQkp7CjFeClJXVcJ2zLNk8fxAZd1lQTMG
//ETbVgjp7cAfWKDvMbA019yGEczB9PI+TdbrK1GgvbnoK+S4RYIGrsYNj+Cs8eBca
//Wfx/r9aHjk2JeAKkO8L3p1po3WrDrxTLNUChkNP+9n4jZZFg9YwzlicNd8SWUnNT
//yPlZspjKadE3JOow9P+3n9dRbD+w7s/PTMTZmgDxWwiXEPziebxJ/HQ4J5c5PNQ/
//LVjAJ7rdBv6ZBmAopP+XWFNIV5xxSlc96Loaxa7uJRCIhhT4CR49Cw+J600w97F3
//EDtA4cv8hc71CV0hSmP/WejLzD6kzAH0Fs2VrTnr4SwsS6QSOBdbB8OEqqhXTseJ
//atPMBSAonXRcwRYHq0L6qZcCAwEAAQ==
//-----END PUBLIC KEY-----";
    private $privatePEMKey = "-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----";
    public $totalShipingCost = 0;
    # checkout
    public function index()
    {
        $carts = Cart::where('user_id', auth()->user()->id)->where('location_id', session('stock_location_id'))->get();

        if (count($carts) > 0) {
            checkCouponValidityForCheckout($carts);
        }

        $user = auth()->user();
        $addresses = $user->addresses()->latest()->get();

        $countries = Country::isActive()->get();

        return getView('pages.checkout.checkout', [
            'carts'     => $carts,
            'user'      => $user,
            'addresses' => $addresses,
            'countries' => $countries,
        ]);
    }

    # checkout logistic
    public function getLogistic(Request $request)
    {
        $logisticZoneCities = LogisticZoneCity::where('city_id', $request->city_id)->distinct('logistic_id')->get();
        return [
            'logistics' => getViewRender('inc.logistics', ['logisticZoneCities' => $logisticZoneCities]),
            'summary'   => getViewRender('pages.partials.checkout.orderSummary', ['carts' => Cart::where('user_id', auth()->user()->id)->where('location_id', session('stock_location_id'))->get()])
        ];
    }

    # checkout shipping amount
    public function getShippingAmount(Request $request)
    {
        $carts              = Cart::where('user_id', auth()->user()->id)->where('location_id', session('stock_location_id'))->get();
        $logisticZone       = LogisticZone::find((int)$request->logistic_zone_id);
//        $shippingAmount     = $logisticZone->standard_delivery_charge;
        $shippingAmount     = 0;
        $cost_per_kg     = $logisticZone->cost_per_kg;
        $additional_cost     = $logisticZone->additional_cost;
        $weight = 0;
        foreach ($carts as $cart){
//            dd($cart);
            $product = $cart->product_variation->product;
            $weight += $product->weight * $cart->qty;
        }
        $shippingAmount = $this->calculateShippingCost($weight,$cost_per_kg,$additional_cost);
        $this->totalShipingCost = $this->calculateShippingCost($weight,$cost_per_kg,$additional_cost);

        return getViewRender('pages.partials.checkout.orderSummary', ['carts' => $carts, 'shippingAmount' => $shippingAmount,'cost_per_kg'=>$cost_per_kg,'additional_cost'=>$additional_cost]);
    }

    function calculateShippingCost($weightInGrams,$cost_per_kg,$additional_cost) {
        $baseCost = $cost_per_kg;

        // Calculate the number of additional 1000g increments beyond the first 1000g
        $additionalIncrements = ceil(($weightInGrams - 1000) / 1000);

        // Calculate the total shipping cost
        $totalCost = $baseCost + ($additionalIncrements * $additional_cost);
//        dd($totalCost);
        return $totalCost;
    }
    # complete checkout process
    public function complete(Request $request)
    {
        $weight = 0;
        $userId = auth()->user()->id;
        $carts  = Cart::where('user_id', $userId)->where('location_id', session('stock_location_id'))->get();

        if (count($carts) > 0) {

            # check if coupon applied -> validate coupon
            $couponResponse = checkCouponValidityForCheckout($carts);
            if ($couponResponse['status'] == false) {
                flash($couponResponse['message'])->error();
                return back();
            }

            # check carts available stock -- todo::[update version] -> run this check while storing OrderItems
            foreach ($carts as $cart) {
//                $productVariationStock = $cart->product_variation->product_variation_stock ? $cart->product_variation->product_variation_stock->stock_qty : 0;
//                $cityid=50;
                $cityid= \Illuminate\Support\Facades\Session::get('city');

                $product = $cart->product_variation->product;
                $branch=\App\Models\Branch::whereHas('cities',function($q) use ($cityid){
                    return $q->where('city_id',$cityid);
                })->get()->last();
                if ($branch){
                    $productPrice=\App\Models\ProductPrice::where('branch_id',$branch->id)
                        ->where('product_id',$product->id)->first();
                    if ($productPrice) {
                        $stock = $productPrice->stock_qty;
//                dd($price);
                    }else{

                        $stock =  0;

                    }
                }else{
                    $stock = $product->variations[0]->product_variation_stock ? $product->variations[0]->product_variation_stock->stock_qty : 0;
                }

                if ($cart->qty > $stock) {
                    $message = $cart->product_variation->product->collectLocalization('name') . ' ' . localize('is out of stock');
                    flash($message)->error();
                    return back();
                }
//            dd($cart);
                    $product = $cart->product_variation->product;
                    $weight += $product->weight * $cart->qty;
            }

            $logisticZone = LogisticZone::where('id', $request->chosen_logistic_zone_id)->first();

            $shippingAmount = $this->calculateShippingCost($weight,$logisticZone->cost_per_kg,$logisticZone->additional_cost);
            # create new order group
            $orderGroup                                     = new OrderGroup;
            $orderGroup->user_id                            = $userId;
            $orderGroup->shipping_address_id                = $request->shipping_address_id;
            $orderGroup->billing_address_id                 = $request->billing_address_id;
            $orderGroup->location_id                        = session('stock_location_id');
            $orderGroup->phone_no                           = $request->phone;
            $orderGroup->alternative_phone_no               = $request->alternative_phone;
            $orderGroup->remarks_box                        = $request->remarks_box;
            $orderGroup->sub_total_amount                   = getSubTotal($carts, false, '', false);
            $orderGroup->total_tax_amount                   = getTotalTax($carts);
            $orderGroup->total_coupon_discount_amount       = 0;
            if (getCoupon() != '') {
                # todo::[for eCommerce] handle coupon for multi vendor
                $orderGroup->total_coupon_discount_amount   = getCouponDiscount(getSubTotal($carts, false), getCoupon());
                # [done->codes below] increase coupon usage counter after successful order
            }
            # todo::[for eCommerce] handle exceptions for standard & express
            $orderGroup->total_shipping_cost                = $shippingAmount;
            $orderGroup->grand_total_amount                 = $orderGroup->sub_total_amount + $orderGroup->total_tax_amount + $orderGroup->total_shipping_cost - $orderGroup->total_coupon_discount_amount;
            $orderGroup->save();

            # order -> todo::[update version] make array for each vendor, create order in loop
            $order = new Order;
            $order->order_group_id  = $orderGroup->id;
            $order->shop_id         = $carts[0]->product_variation->product->shop_id;
            $order->user_id         = $userId;
            $order->location_id     = session('stock_location_id');
            if (getCoupon() != '') {
                $order->applied_coupon_code         = getCoupon();
                $order->coupon_discount_amount      = $orderGroup->total_coupon_discount_amount; // todo::[update version] calculate for each vendors
            }
            $order->total_admin_earnings            = $orderGroup->grand_total_amount;
            $order->logistic_id                     = $logisticZone->logistic_id;
            $order->logistic_name                   = optional($logisticZone->logistic)->name;
            $order->shipping_delivery_type          = $request->shipping_delivery_type;

            if ($request->shipping_delivery_type == getScheduledDeliveryType()) {
                $timeSlot = ScheduledDeliveryTimeList::where('id', $request->timeslot)->first(['id', 'timeline']);
                $timeSlot->scheduled_date = $request->scheduled_date;
                $order->scheduled_delivery_info = json_encode($timeSlot);
            }



            $order->shipping_cost                   = $shippingAmount; // todo::[update version] calculate for each vendors

            $branch = '';
            if (\Illuminate\Support\Facades\Session::has('city')){
                $cityid= \Illuminate\Support\Facades\Session::get('city');
                $branch=\App\Models\Branch::whereHas('cities',function($q) use ($cityid){
                    return $q->where('city_id',$cityid);
                })->get()->last();
            }
            $order->branch_name = $branch->name;
            $order->save();

            # order items
            foreach ($carts as $cart) {
                $orderItem                       = new OrderItem;
                $orderItem->order_id             = $order->id;
                $orderItem->product_variation_id = $cart->product_variation_id;
                $orderItem->qty                  = $cart->qty;
                $orderItem->location_id     = session('stock_location_id');
                $orderItem->unit_price           = variationDiscountedPrice($cart->product_variation->product, $cart->product_variation);
                $orderItem->total_tax            = variationTaxAmount($cart->product_variation->product, $cart->product_variation);
                $orderItem->total_price          = $orderItem->unit_price * $orderItem->qty;
                $orderItem->save();

                $product = $cart->product_variation->product;
                $product->total_sale_count += $orderItem->qty;
                // minus stock qty

                try {
                    $productVariationStock = $cart->product_variation->product_variation_stock;


                    $cityid= \Illuminate\Support\Facades\Session::get('city');

                    $branch=\App\Models\Branch::whereHas('cities',function($q) use ($cityid){
                        return $q->where('city_id',$cityid);
                    })->get()->last();
                    if ($branch){
                        $productPrice=\App\Models\ProductPrice::where('branch_id',$branch->id)
                            ->where('product_id',$product->id)->first();
                        if ($productPrice) {
                            $stoctqty = ProductPrice::findOrfail($productPrice->id);
                            $stoctqty->stock_qty -= $orderItem->qty;
                            $stoctqty->save();
                        }
                        }else{
                            $productVariationStock->stock_qty -= $orderItem->qty;
                            $productVariationStock->save();

                        }
                } catch (\Throwable $th) {
                    //throw $th;
                }
//                $stoctqty->stock_qty -= $orderItem->qty;

                $cityid= \Illuminate\Support\Facades\Session::get('city');

                $branch=\App\Models\Branch::whereHas('cities',function($q) use ($cityid){
                    return $q->where('city_id',$cityid);
                })->get()->last();
                if ($branch){
                    $productPrice=\App\Models\ProductPrice::where('branch_id',$branch->id)
                        ->where('product_id',$product->id)->first();
                    if ($productPrice) {
                        $stoctqty = ProductPrice::findOrfail($productPrice->id);
                        $stoctqty->stock_qty = $stoctqty->stock_qty - $orderItem->qty;
                        $stoctqty->save();

                    }else{
                        $product->stock_qty -= $orderItem->qty;
                    }
                }else{
                    $product->stock_qty -= $orderItem->qty;
                    $product->save();
                }

                if ($product->categories()->count() > 0) {
                    foreach ($product->categories as $category) {
                        $category->total_sale_count += $orderItem->qty;
//                        $category->save();
                    }
                }

                $cart->delete();
            }


            # increase coupon usage
            if (getCoupon() != '' && $orderGroup->total_coupon_discount_amount > 0) {
                $coupon = Coupon::where('code', getCoupon())->first();
                $coupon->total_usage_count += 1;
                $coupon->save();

                # coupon usage by user
                $couponUsageByUser = CouponUsage::where('user_id', auth()->user()->id)->where('coupon_code', $coupon->code)->first();
                if (!is_null($couponUsageByUser)) {
                    $couponUsageByUser->usage_count += 1;
                } else {
                    $couponUsageByUser = new CouponUsage;
                    $couponUsageByUser->usage_count = 1;
                    $couponUsageByUser->coupon_code = getCoupon();
                    $couponUsageByUser->user_id = $userId;
                }
                $couponUsageByUser->save();
                removeCoupon();
            }

            $address = UserAddress::where('id',$request->shipping_address_id)->first();
            $city = City::where('id',$address->city_id)->first();

            # payment gateway integration & redirection
            if ($request->payment_method == "hbl_pay") {

                $stringData='
    {

          "USER_ID": "esajeesadmin",

        "PASSWORD": "X2YGc5W6@",

        "CLIENT_NAME": "esajeesadmin",

        "RETURN_URL": "'.url('/hbl/success').'",

        "CANCEL_URL": "'.url('/hbl/fail').'",

        "CHANNEL": "HBLPay_Esajees_Website",

        "TYPE_ID": "0",

     "ORDER": {

            "DISCOUNT_ON_TOTAL": "'.$orderGroup->total_coupon_discount_amount.'",

            "SUBTOTAL": "'.$orderGroup->grand_total_amount .'",

            "OrderSummaryDescription": [{

                "ITEM_NAME": "Product 1",

                "QUANTITY": "1",

                "UNIT_PRICE": "'.$orderGroup->grand_total_amount .'",

                "OLD_PRICE": "0",

                "CATEGORY": "Test Category",

                "SUB_CATEGORY": "Test Sub Category"

            }

            ]

        },

        "SHIPPING_DETAIL": {

            "NAME": "null",

            "ICON_PATH": null,

            "DELIEVERY_DAYS": "0",

            "SHIPPING_COST": "0"

        },

       "ADDITIONAL_DATA": {

            "REFERENCE_NUMBER": "'.$orderGroup->order_code.'",

            "CUSTOMER_ID": "'.Auth::user()->id.'",

            "CURRENCY": "PKR",

            "BILL_TO_FORENAME": "'.Auth::user()->name.'",

            "BILL_TO_SURNAME": "Surname",

            "BILL_TO_EMAIL": "'.(Auth::user()->email ? Auth::user()->email : 'demo@email').'",

            "BILL_TO_PHONE": "'.(Auth::user()->phone ? Auth::user()->phone : '123456' ).'",

            "BILL_TO_ADDRESS_LINE": "'.$address->address.'",

            "BILL_TO_ADDRESS_CITY": "'.$city->name.'",

            "BILL_TO_ADDRESS_STATE": "SD",

            "BILL_TO_ADDRESS_COUNTRY": "PK",

            "BILL_TO_ADDRESS_POSTAL_CODE": "75400",

            "SHIP_TO_FORENAME": "'.Auth::user()->name.'",

            "SHIP_TO_SURNAME": "Surname",

            "SHIP_TO_EMAIL": "'.(Auth::user()->email ? Auth::user()->email : 'demo@gmail.com' ).'",

            "SHIP_TO_PHONE": "'.($address->phone ? $address->phone : '1234567' ).'",

            "SHIP_TO_ADDRESS_LINE": "Test street",

            "SHIP_TO_ADDRESS_CITY": "'.($city->name ? $city->name : 'Karachi' ).'",

            "SHIP_TO_ADDRESS_STATE": "SD",

            "SHIP_TO_ADDRESS_COUNTRY": "PK",

            "SHIP_TO_ADDRESS_POSTAL_CODE": "75400",

            "MerchantFields": {

                "MDD1": "WC",

                "MDD2": "YES",

                "MDD3": "Product Category",

                "MDD4": "Product Name",

                "MDD5": "No",

                "MDD6": "Standard",

                "MDD7": "1",

                "MDD8": "Pakistan",

                "MDD20": "NO"

            }

        }

    }

    ';


                $arrJson=json_decode($stringData,true);
                $arrJson=json_encode($this->recParamsEncryption($arrJson,$this->publicPEMKey));

//                $url="https://testpaymentapi.hbl.com/hblpay/api/checkout";
                $url="https://digitalbankingportal.hbl.com/hostedcheckout/api/checkout";

//debug(callAPI("POST",$url,$cyb->encrypt_RSA($stringData)));

                $jsonCyberSourceResult=json_decode(callAPI("POST",$url,$arrJson),true);

//                dd($jsonCyberSourceResult);
                if ($jsonCyberSourceResult["IsSuccess"] && $jsonCyberSourceResult["ResponseMessage"]=="Success" && $jsonCyberSourceResult["ResponseCode"]==0){
                    $sessionId=base64_encode($jsonCyberSourceResult["Data"]["SESSION_ID"]);
                    $url = 'https://digitalbankingportal.hbl.com/hostedcheckout/site/index.html#/checkout?data='.$sessionId;
                    $request->session()->put('payment_type', 'order_payment');
                    $request->session()->put('payment_method', $request->payment_method);
                    $request->session()->put('order_code', $orderGroup->order_code);
                    return  redirect($url);
                }else{

                    $orderGroup->order->orderItems()->delete();
                    $orderGroup->order()->delete();
                    $orderGroup->delete();
                    flash(localize('Payment failed, please try again'))->error();

                    return redirect()->back();

                }

            }elseif($request->payment_method != "cod"){
                $orderGroup->payment_method = $request->payment_method;
                $orderGroup->save();

                $request->session()->put('payment_type', 'order_payment');
                $request->session()->put('order_code', $orderGroup->order_code);
                $request->session()->put('payment_method', $request->payment_method);

                # init payment
                $payment = new PaymentsController;
                return $payment->initPayment();
            } else {

                flash(localize('Your order has been placed successfully'))->success();
                return redirect()->route('checkout.success', $orderGroup->order_code);
            }
        }

        flash(localize('Your cart is empty'))->error();
        return back();
    }

    # order successful
    public function success($code,Request $request)
    {
        dd($request->all());
        $orderGroup = OrderGroup::where('user_id', auth()->user()->id)->where('order_code', $code)->first();
        $user = auth()->user();

        try {
            Mail::send('backend.pages.orders.invoice', ['order' => $orderGroup->order], function ($message) use ($user) {
                $message->from(env('MAIL_FROM_ADDRESS'))
                    ->to($user->email)
                    ->subject(localize('Order Placed') . ' - ' . env('APP_NAME'));
            });
            Notification::send($user, new OrderPlacedNotification($orderGroup->order));
        } catch (\Exception $e) {
        }
        return getView('pages.checkout.invoice', ['orderGroup' => $orderGroup]);
    }

    # update payment status
    public function updatePayments($payment_details)
    {
        $orderGroup = OrderGroup::where('order_code', session('order_code'))->first();
        $payment_method = session('payment_method');

        $orderGroup->payment_status = paidPaymentStatus();
        $orderGroup->order->update(['payment_status' => paidPaymentStatus()]); # for multi-vendor loop through each orders & update 

        $orderGroup->payment_method = $payment_method;
        $orderGroup->payment_details = $payment_details;
        $orderGroup->save();

        clearOrderSession();
        flash(localize('Your order has been placed successfully'))->success();
        return redirect()->route('checkout.success', $orderGroup->order_code);
    }

    public function hblSuccess(Request $request){

//        dd($request->data);
        $encryptedData = $request->data;

        $url_params = $this->decryptData($encryptedData, $this->privatePEMKey);
        dd($url_params);
        $splitToArray = explode("&",$url_params);
        $responseCode = str_replace("RESPONSE_CODE=","",$splitToArray[0]);
        $responseMsg = str_replace("RESPONSE_MESSAGE=","",$splitToArray[1]);
        $orderRefNumber = str_replace("ORDER_REF_NUMBER=","",$splitToArray[2]);
        $paymentType=str_replace("PAYMENT_TYPE=","",$splitToArray[3]);
        $orderGroup = OrderGroup::where('order_code', session('order_code'))->first();
        $payment_method = "HBL Pay";

        $orderGroup->payment_status = paidPaymentStatus();
        $orderGroup->order->update(['payment_status' => paidPaymentStatus()]); # for multi-vendor loop through each orders & update

        $orderGroup->payment_method = $payment_method;
        $orderGroup->payment_details = null;
        $orderGroup->save();

        clearOrderSession();
        flash(localize('Your order has been placed successfully'))->success();
        return redirect()->route('checkout.success', $orderGroup->order_code);
    }

    public function hblFail(){
        $orderGroup = OrderGroup::where('order_code', session('order_code'))->first();
//        if (getSetting('enable_cod') == 1) {
//            # order success
//            clearOrderSession();
//            flash(localize('Payment failed, Please pay in cash on delivery'))->success();
//            return getView('pages.checkout.invoice', ['orderGroup' => $orderGroup]);
//        } else {
            # delete order
            $orderGroup->order->orderItems()->delete();
            $orderGroup->order()->delete();
            $orderGroup->delete();
            clearOrderSession();
            flash(localize('Payment failed, please try again'))->error();
            return redirect()->route('home');
//        }
    }
    public function rsaEncryptCyb($plainData, $publicPEMKey=null){

        if(!$publicPEMKey)

            $publicPEMKey= $this->publicPEMKey;



        $encryptionOk = openssl_public_encrypt ($plainData, $encryptedData, $publicPEMKey, OPENSSL_PKCS1_PADDING);



        if($encryptionOk === false){

            return false;

        }

        return base64_encode($encryptedData);



        return false;

    }

  public function recParamsEncryption($arrJson,$cyb){

        foreach($arrJson as $jsonIndex => $jsonValue){

            if( !is_array($jsonValue))

                if($jsonIndex!=="USER_ID")

                    $arrJson[$jsonIndex]=$this->rsaEncryptCyb($jsonValue);

                else

                    $arrJson[$jsonIndex]=$jsonValue;

            else{
                $arrJson[$jsonIndex]=$this->recParamsEncryption($jsonValue,$cyb);
            }

        }

        return $arrJson;
    }


    function decryptData($data, $privatePEMKey)
    {
        $DECRYPT_BLOCK_SIZE = 512;
        $decrypted = '';

        $data = str_split(base64_decode($data), $DECRYPT_BLOCK_SIZE);
        foreach($data as $chunk)
        {
            $partial = '';

            $decryptionOK = openssl_private_decrypt($chunk, $partial, $privatePEMKey, OPENSSL_PKCS1_PADDING);

            if($decryptionOK === false)
            {
                $decrypted = '';
                return $decrypted;
            }
            $decrypted .= $partial;
        }

        return utf8_decode($decrypted);
    }
}
