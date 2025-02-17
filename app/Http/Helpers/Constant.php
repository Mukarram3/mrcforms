<?php

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Localization;
use App\Models\MediaManager;
use App\Models\SystemSetting;
use App\Models\Variation;
use App\Models\VariationValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

if (!function_exists('getTheme')) {
    # get system theme
    function getTheme()
    {
        if (session('theme') != null && session('theme') != '') {
            return session('theme');
        }
        return Config::get('app.theme');
    }
}

if (!function_exists('getView')) {
    # get view of theme
    function getView($path, $data = [])
    {
        return view('frontend.' . getTheme() . '.' . $path, $data);
    }
}

if (!function_exists('getViewRender')) {
    # get view of theme with render
    function getViewRender($path, $data = [])
    {
        return view('frontend.' . getTheme() . '.' . $path, $data)->render();
    }
}

if (!function_exists('cacheClear')) {
    # clear server cache
    function cacheClear()
    {
        try {
            Artisan::call('cache:forget spatie.permission.cache');
        } catch (\Throwable $th) {
            //throw $th;
        }

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
    }
}

if (!function_exists('clearOrderSession')) {
    # clear session cache
    function clearOrderSession()
    {
        session()->forget('payment_method');
        session()->forget('payment_type');
        session()->forget('order_code');
    }
}

if (!function_exists('csrfToken')) {
    #  Get the CSRF token value. 
    function csrfToken()
    {
        $session = app('session');

        if (isset($session)) {
            return $session->token();
        }
        throw new RuntimeException('Session store not set.');
    }
}

if (!function_exists('paginationNumber')) {
    # return number of data per page
    function paginationNumber($value = null)
    {
        return $value != null ? $value : env('DEFAULT_PAGINATION');
    }
}

if (!function_exists('areActiveRoutes')) {
    # return active class
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
        return '';
    }
}


if (!function_exists('staticAsset')) {
    # return path for static assets
    function staticAsset($path, $secure = null)
    {
        if (str_contains(url('/'), '.test') || str_contains(url('/'), 'http://127.0.0.1:')) {
            return app('url')->asset('' . $path, $secure) . '?v=' . env('APP_VERSION');
        }
        return app('url')->asset('public/' . $path, $secure) . '?v=' . env('APP_VERSION');
    }
}

if (!function_exists('uploadedAsset')) {
    #  Generate an asset path for the uploaded files. 
    function uploadedAsset($fileId)
    {
        $mediaFile = MediaManager::find($fileId);
        if (!is_null($mediaFile)) {
                if (str_contains(url('/'), '.test') || str_contains(url('/'), 'http://127.0.0.1:')) {
                    return app('url')->asset('' . $mediaFile->media_file);
                }
            return app('url')->asset('public/' . $mediaFile->media_file);

        }
        return '';
    }
}


if (!function_exists('localize')) {
    # add / return localization 
    function localize($key, $lang = null)
    {
        if ($lang == null) {
            $lang = App::getLocale();
        }

        $t_key = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($key)));

        $localization_default = Cache::rememberForever('localizations-' . env('DEFAULT_LANGUAGE', 'en'), function () {
            return Localization::where('lang_key', env('DEFAULT_LANGUAGE', 'en'))->pluck('t_value', 't_key');
        });

        if (!isset($localization_default[$t_key])) {
            # add new localization
            newLocalization(env('DEFAULT_LANGUAGE', 'en'), $t_key, $key);
        }

        # return user session lang
        $localization_user = Cache::rememberForever("localizations-{$lang}", function () use ($lang) {
            return Localization::where('lang_key', $lang)->pluck('t_value', 't_key')->toArray();
        });

        if (isset($localization_user[$t_key])) {
            return trim($localization_user[$t_key]);
        }

        return trim(__($t_key));
    }
}

if (!function_exists('newLocalization')) {
    # new localization
    function newLocalization($lang, $t_key, $key)
    {
        $localization = new Localization;
        $localization->lang_key = $lang;
        $localization->t_key = $t_key;
        $localization->t_value = str_replace(array("\r", "\n", "\r\n"), "", $key);
        $localization->save();

        # clear cache
        Cache::forget('localizations-' . $lang);

        return trim($key);
    }
}

if (!function_exists('writeToEnvFile')) {
    # write To Env File
    function
    writeToEnvFile($type, $val)
    {
        if (env('DEMO_MODE') != 'On') {
            $path = base_path('.env');
            if (file_exists($path)) {
                $val = '"' . trim($val) . '"';
                if (is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0) {
                    file_put_contents($path, str_replace(
                        $type . '="' . env($type) . '"',
                        $type . '=' . $val,
                        file_get_contents($path)
                    ));
                } else {
                    file_put_contents($path, file_get_contents($path) . "\r\n" . $type . '=' . $val);
                }
            }
        }
    }
}

if (!function_exists('getFileType')) {
    #  Get file Type
    function getFileType($type)
    {
        $fileTypeArray = [
            // audio
            "mp3"       =>  "audio",
            "wma"       =>  "audio",
            "aac"       =>  "audio",
            "wav"       =>  "audio",

            // video
            "mp4"       =>  "video",
            "mpg"       =>  "video",
            "mpeg"      =>  "video",
            "webm"      =>  "video",
            "ogg"       =>  "video",
            "avi"       =>  "video",
            "mov"       =>  "video",
            "flv"       =>  "video",
            "swf"       =>  "video",
            "mkv"       =>  "video",
            "wmv"       =>  "video",

            // image 
            "png"       =>  "image",
            "svg"       =>  "image",
            "gif"       =>  "image",
            "jpg"       =>  "image",
            "jpeg"      =>  "image",
            "webp"      =>  "image",

            // document 
            "doc"       =>  "document",
            "txt"       =>  "document",
            "docx"      =>  "document",
            "pdf"       =>  "document",
            "csv"       =>  "document",
            "xml"       =>  "document",
            "ods"       =>  "document",
            "xlr"       =>  "document",
            "xls"       =>  "document",
            "xlsx"      =>  "document",

            // archive  
            "zip"       =>  "archive",
            "rar"       =>  "archive",
            "7z"        =>  "archive"
        ];
        return isset($fileTypeArray[$type]) ? $fileTypeArray[$type] : null;
    }
}

if (!function_exists('fileDelete')) {
    # file delete 
    function fileDelete($file)
    {
        if (File::exists('public/' . $file)) {
            File::delete('public/' . $file);
        }
    }
}

if (!function_exists('getSetting')) {
    # return system settings value
    function getSetting($key, $default = null)
    {
        try {
            $settings = Cache::remember('settings', 86400, function () {
                return SystemSetting::all();
            });

            $setting = $settings->where('entity', $key)->first();

            return $setting == null ? $default : $setting->value;
        } catch (\Throwable $th) {
            return $default;
        }
    }
}

if (!function_exists('renderStarRating')) {
    # render ratings
    function renderStarRating($rating, $maxRating = 5)
    {
        $fullStar = "<i data-feather='star' width='16' height='16' class='text-primary'></i>";

        $rating = $rating <= $maxRating ? $rating : $maxRating;
        $fullStarCount = (int)$rating;

        $html = str_repeat($fullStar, $fullStarCount);
        echo $html;
    }
}

if (!function_exists('renderStarRatingFront')) {
    # render ratings frontend
    function renderStarRatingFront($rating, $maxRating = 5)
    {
        $fullStar = '<li><i class="fas fa-star"></i></li>';

        $rating = $rating <= $maxRating ? $rating : $maxRating;
        $fullStarCount = (int)$rating;

        $html = str_repeat($fullStar, $fullStarCount);
        echo $html;
    }
}

if (!function_exists('formatPrice')) {
    //formats price - truncate price to 1M, 2K if activated by admin 
    function formatPrice($price, $truncate = false, $forceTruncate = false)
    {

        // convert amount equal to local currency
        if (Session::has('currency_code') && Session::has('local_currency_rate')) {

//
//            $price = floatval($price) / (floatval(env('DEFAULT_CURRENCY_RATE')) || 1);
//            $price = floatval($price) * floatval(Session::get('local_currency_rate'));

            $price = floatval($price) / 1;
            $price = floatval($price) * 1;


        }

        // truncate price
        if ($truncate) {
            if (getSetting('truncate_price') == 1 || $forceTruncate == true) {
                if ($price < 1000000) {
                    // less than a million
                    $price = number_format($price, getSetting('no_of_decimals'));
                } else if ($price < 1000000000) {
                    // less than a billion
                    $price = number_format($price / 1000000, getSetting('no_of_decimals')) . 'M';
                } else {
                    // at least a billion
                    $price = number_format($price / 1000000000, getSetting('no_of_decimals')) . 'B';
                }
            }
        } else {
            // decimals
            if (getSetting('no_of_decimals') > 0) {
                $price = number_format($price, getSetting('no_of_decimals'));
            } else {
                $price = number_format($price, getSetting('no_of_decimals'), '.', ',');
            }
        }

        // currency symbol
        $symbol             = Session::has('currency_symbol')           ? Session::get('currency_symbol')           : env('DEFAULT_CURRENCY_SYMBOL');
        $symbolAlignment    = Session::has('currency_symbol_alignment') ? Session::get('currency_symbol_alignment') : env('DEFAULT_CURRENCY_SYMBOL_ALIGNMENT');

        if ($symbolAlignment == 0) {
            return "Rs." . $price;
        }
        return $price . $symbol;
    }
}


if (!function_exists('priceToUsd')) {
    // price to usd
    function priceToUsd($price)
    {
        // convert amount equal to local currency
        if (Session::has('currency_code') && Session::has('local_currency_rate')) {
            $price = floatval($price) / floatval(Session::get('local_currency_rate'));
            $price = floatval($price) / floatval(1);
        }

        return $price;
    }
}


if (!function_exists('productBasePrice')) {
    // min/base price of a product
    function productBasePrice($product, $formatted = false)
    {
        $price = $product->productPrices[0]['max_price'];

//        if (\Illuminate\Support\Facades\Session::has('branch_id')) {
//            $branch_id = \Illuminate\Support\Facades\Session::get('branch_id');
//            $branch = \App\Models\Branch::where('id', $branch_id)->first();
//
//            if ($branch) {
//                $productPrice = \App\Models\ProductPrice::where('branch_id', $branch->id)
//                    ->where('product_id', $product->id)
//                    ->value('max_price');
//
//                if ($productPrice !== null) {
//                    $price = $productPrice;
//                }
//            }
//        } else {
//            $cityId = \Illuminate\Support\Facades\Session::get('city');
//
//            $branch = \App\Models\Branch::whereHas('cities', function ($q) use ($cityId) {
//                $q->where('city_id', $cityId);
//            })->latest()->first();
//
//            if ($branch) {
//                $productPrice = \App\Models\ProductPrice::where('branch_id', $branch->id)
//                    ->where('product_id', $product->id)
//                    ->value('max_price');
//
//                if ($productPrice !== null) {
//                    $price = $productPrice;
//                }
//            }
//        }



        $tax = 0;

        foreach ($product->taxes as $productTax) {
            if ($productTax->tax_type == 'percent') {
                $tax += ($price * $productTax->tax_value) / 100;
            } elseif ($productTax->tax_type == 'flat') {
                $tax += $productTax->tax_value;
            }
        }

        $price += $tax;
//        dd($price);
        return $formatted ? formatPrice($price) : $price;
    }
}

if (!function_exists('discountedProductBasePrice')) {
    // min/base price of a product with discount
    function discountedProductBasePrice($product, $formatted = false)
    {
        $price = $product->productPrices[0]['max_price'];
//        $price = $product->min_price;
//        $cityid=50;
//        $cityid= \Illuminate\Support\Facades\Session::get('city');
//
//        $branch=\App\Models\Branch::whereHas('cities',function($q) use ($cityid){
//            return $q->where('city_id',$cityid);
//        })->get()->last();
//        if ($branch){
//            $productPrice=\App\Models\ProductPrice::where('branch_id',$branch->id)
//                ->where('product_id',$product->id)->first();
//            if ($productPrice) {
//                $price = $productPrice->min_price;
////                dd($price);
//            }else{
//
//                $price = $product->min_price;
//            }
//        }else{
//            $price = $product->min_price;
//        }

        $discount_applicable = false;

        if ($product->discount_start_date == null || $product->discount_end_date == null) {
            $discount_applicable = false;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount_value) / 100;
            } elseif ($product->discount_type == 'flat') {
                $price -= $product->discount_value;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $price += ($price * $product_tax->tax_value) / 100;
            } elseif ($product_tax->tax_type == 'flat') {
                $price += $product_tax->tax_value;
            }
        }

        return $formatted ? formatPrice($price) : $price;
    }
}

if (!function_exists('productMaxPrice')) {
    // max price of a product
    function productMaxPrice($product, $formatted = false)
    {
        $price = $product->max_price;
        $tax = 0;

        foreach ($product->taxes as $productTax) {
            if ($productTax->tax_type == 'percent') {
                $tax += ($price * $productTax->tax_value) / 100;
            } elseif ($productTax->tax_type == 'flat') {
                $tax += $productTax->tax_value;
            }
        }

        $price += $tax;
        return $formatted ? formatPrice($price) : $price;
    }
}

if (!function_exists('discountedProductMaxPrice')) {
    // max price of a product with discount
    function discountedProductMaxPrice($product, $formatted = false)
    {
//        $cityid= \Illuminate\Support\Facades\Session::get('city');
//
////        $cityid=50;
//        $branch=\App\Models\Branch::whereHas('cities',function($q) use ($cityid){
//            return $q->where('city_id',$cityid);
//        })->get()->last();
//        if ($branch){
//            $productPrice=\App\Models\ProductPrice::where('branch_id',$branch->id)
//                ->where('product_id',$product->id)->first();
//            if ($productPrice) {
//                $price = $productPrice->max_price;
////                dd($price);
//            }else{
//
//                $price = $product->max_price;
//            }
//        }else{
//            $price = $product->max_price;
//        }
        $price = $product->productPrices[0]['max_price'];


        $discount_applicable = false;

        if ($product->discount_start_date == null || $product->discount_end_date == null) {
            $discount_applicable = false;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount_value) / 100;
            } elseif ($product->discount_type == 'flat') {
                $price -= $product->discount_value;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $price += ($price * $product_tax->tax_value) / 100;
            } elseif ($product_tax->tax_type == 'flat') {
                $price += $product_tax->tax_value;
            }
        }

        return $formatted ? formatPrice($price) : $price;
    }
}

if (!function_exists('discountPercentage')) {
    // return discount in %
    function discountPercentage($product)
    {
        $discountPercentage = $product->discount_value;

        if ($product->discount_type != "percent") {
            $price = productBasePrice($product);
            $discountAmount = discountedProductBasePrice($product);
            $discountValue = $price - $discountAmount;
            $discountPercentage = ($discountValue * 100) / ($price > 0 ? $price : 1);
        }

        return round($discountPercentage);
    }
}

if (!function_exists('sellCountPercentage')) {
    // return sales count %
    function sellCountPercentage($product)
    {
        $sold = $product->total_sale_count;
        $target = (int) $product->sell_target;
        $salePercentage = ($sold * 100) / ($target > 0 ? $target : 1);
        return round($salePercentage);
    }
}

if (!function_exists('generateVariationOptions')) {
    //  generate combinations based on variations
    function generateVariationOptions($options)
    {
        if (count($options) == 0) {
            return $options;
        }
        $variation_ids = array();
        foreach ($options as $option) {

            $value_ids = array();
            if (isset($variation_ids[$option->variation_id])) {
                $value_ids = $variation_ids[$option->variation_id];
            }
            if (!in_array($option->variation_value_id, $value_ids)) {
                array_push($value_ids, $option->variation_value_id);
            }
            $variation_ids[$option->variation_id] = $value_ids;
        }
        $options = array();
        foreach ($variation_ids as $id => $values) {
            $variationValues = array();
            foreach ($values as $value) {
                $variationValue = VariationValue::find($value);
                $val = array(
                    'id'   => $value,
                    'name' => $variationValue->collectLocalization('name'),
                    'code' => $variationValue->color_code
                );
                array_push($variationValues, $val);
            }
            $data['id'] = $id;
            $data['name'] = Variation::find($id)->collectLocalization('name');
            $data['values'] = $variationValues;

            array_push($options, $data);
        }
        return $options;
    }
}

if (!function_exists('variationPrice')) {
    // return price of a variation
    function variationPrice($product, $variation)
    {
        $price = $variation->price;

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $price += ($price * $product_tax->tax_value) / 100;
            } elseif ($product_tax->tax_type == 'flat') {
                $price += $product_tax->tax_value;
            }
        }
        return $price;
    }
}

if (!function_exists('variationDiscountedPrice')) {
    // return discounted price of a variation
    function variationDiscountedPrice($product, $variation, $addTax = true)
    {
//        $price = $variation->price;
//        $cityid=50;
        $cityid= \Illuminate\Support\Facades\Session::get('city');

        $branch=\App\Models\Branch::whereHas('cities',function($q) use ($cityid){
            return $q->where('city_id',$cityid);
        })->get()->last();
        if ($branch){
            $productPrice=\App\Models\ProductPrice::where('branch_id',$branch->id)
                ->where('product_id',$product->id)->first();
            if ($productPrice) {
                $price = $productPrice->min_price;
//                dd($price);
            }else{
                $price = $product->min_price;
            }
        }else{
            $price = $product->min_price;
        }

        $discount_applicable = false;


        if ($product->discount_start_date == null || $product->discount_end_date == null) {
            $discount_applicable = false;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount_value) / 100;
            } elseif ($product->discount_type == 'flat') {
                $price -= $product->discount_value;
            }
        }

        if ($addTax) {
            foreach ($product->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $price += ($price * $product_tax->tax_value) / 100;
                } elseif ($product_tax->tax_type == 'flat') {
                    $price += $product_tax->tax_value;
                }
            }
        }

        return $price;
    }
}

if (!function_exists('variationTaxAmount')) {
    // return tax of a variation
    function variationTaxAmount($product, $variation)
    {
        $price = $variation->price;
        $tax   = 0;

        $discount_applicable = false;

        if ($product->discount_start_date == null || $product->discount_end_date == null) {
            $discount_applicable = false;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount_value) / 100;
            } elseif ($product->discount_type == 'flat') {
                $price -= $product->discount_value;
            }
        }

        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax_value) / 100;
            } elseif ($product_tax->tax_type == 'flat') {
                $tax += $product_tax->tax_value;
            }
        }

        return $tax;
    }
}

if (!function_exists('getSubTotal')) {
    // return sub total price
    function getSubTotal($carts, $couponDiscount = true, $couponCode = '', $addTax = true)
    {


        $price = 0;
        $amount = 0;
        if (count($carts) > 0) {
            foreach ($carts as $cart) {
                $product    = $cart->product_variation->product;
                $variation  = $cart->product_variation;

                $discountedVariationPriceWithTax = variationDiscountedPrice($product, $variation, $addTax);

                if (\Illuminate\Support\Facades\Session::has('branch_id')) {
                    $branch_id = \Illuminate\Support\Facades\Session::get('branch_id');
                    $branch = \App\Models\Branch::where('id', $branch_id)->first();
                    if ($branch) {
                        $productPrice = \App\Models\ProductPrice::where('branch_id', $branch->id)
                            ->where('product_id', $product->id)
                            ->value('min_price');

                        if ($productPrice !== null) {
                            $price = $productPrice;
                        }
                    }
                }



                $price += (float) $discountedVariationPriceWithTax * $cart->qty;
            }

            # calculate coupon discount
            if ($couponDiscount) {
                $amount = getCouponDiscount($price, $couponCode);
            }
        }


        return $price - $amount;
    }
}

if (!function_exists('setCoupon')) {
    // set coupon code in cookie
    function setCoupon($coupon)
    {
        $theTime = time() + 86400 * 7;
        setcookie('coupon_code', $coupon->code, $theTime, '/'); // 86400 = 1 day
    }
}

if (!function_exists('removeCoupon')) {
    // remove coupon code from  cookie
    function removeCoupon()
    {
        if (isset($_COOKIE["coupon_code"])) {
            setcookie("coupon_code", "", time() - 3600);
            unset($_COOKIE["coupon_code"]);
        }
    }
}

if (!function_exists('getCoupon')) {
    // get coupon code from  cookie
    function getCoupon()
    {
        if (isset($_COOKIE["coupon_code"])) {
            return $_COOKIE["coupon_code"];
        }
        return '';
    }
}

if (!function_exists('getCouponDiscount')) {
    // return Coupon Discount amount
    function getCouponDiscount($subTotal, $code = '')
    {
        $amount = 0;
        $coupon = Coupon::where('code', $code)->first();
        if ($coupon) {
            $date = strtotime(date('d-m-Y H:i:s'));
            # check if coupon is not expired
            if ($coupon->start_date <= $date && $coupon->end_date >= $date) {
                if ($coupon->discount_type == 'flat') {
                    $amount = (float) $coupon->discount_value;
                } else {
                    $amount = ((float) $coupon->discount_value * $subTotal) / 100;
                    if ($amount > (float) $coupon->max_discount_amount) {
                        $amount = (float) $coupon->max_discount_amount;
                    }
                }
            } else {
                removeCoupon();
            }
        } else {
            removeCoupon();
        }

        return $amount;
    }
}

if (!function_exists('validateCouponForProductsAndCategories')) {
    # check coupon for products & categories
    function validateCouponForProductsAndCategories($cartItems, $coupon)
    {
        if ($coupon->product_ids) {
            $product_ids = json_decode($coupon->product_ids);
            foreach ($cartItems as $key => $cartItem) {
                if (in_array($cartItem->product_variation->product_id, $product_ids)) {
                    return true;
                }
            }
        }

        if ($coupon->category_ids) {
            $category_ids = json_decode($coupon->category_ids);
            foreach ($cartItems as $key => $cartItem) {
                $product_categories = $cartItem->product_variation->product->product_categories;
                foreach ($product_categories as $key => $product_category) {
                    if (in_array($product_category->category_id, $category_ids)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

if (!function_exists('checkCouponValidityForCheckout')) {
    // check coupon validity For Checkout
    function checkCouponValidityForCheckout($carts)
    {
        if (isset($_COOKIE["coupon_code"])) {
            $date = strtotime(date('d-m-Y H:i:s'));
            $coupon = Coupon::where('code', $_COOKIE["coupon_code"])->first();
            if ($coupon) {
                # total coupon usage
                $totalCouponUsage = CouponUsage::where('coupon_code', $coupon->code)->sum('usage_count');
                if ($totalCouponUsage == $coupon->total_usage_limit) {
                    # coupon usage limit reached  
                    removeCoupon();
                    return [
                        'status'    => false,
                        'message'   => localize('Total usage limit has been reached for the coupon')
                    ];
                }

                # coupon usage by user
                $couponUsageByUser = CouponUsage::where('user_id', auth()->user()->id)->where('coupon_code', $coupon->code)->first();
                if (!is_null($couponUsageByUser)) {
                    if ($couponUsageByUser->usage_count ==  $coupon->customer_usage_limit) {
                        removeCoupon();
                        return [
                            'status'    => false,
                            'message'   => localize('You have used this coupon for maximum time')
                        ];
                    }
                }

                # check if coupon is expired
                if ($coupon->start_date <= $date && $coupon->end_date >= $date) {
                    $subTotal = (float) getSubTotal($carts, false);
                    if ($subTotal >= (float) $coupon->min_spend) {
                        # check if coupon is for categories or products
                        if ($coupon->product_ids || $coupon->category_ids) {
                            if (!validateCouponForProductsAndCategories($carts, $coupon)) {
                                # coupon not valid for your cart items  
                                removeCoupon();
                                return [
                                    'status'    => false,
                                    'message'   => localize('Coupon is not valid for the products')
                                ];
                            }

                            return [
                                'status'    => true,
                                'message'   => ''
                            ];
                        }

                        return [
                            'status'    => true,
                            'message'   => ''
                        ];
                    } else {
                        # min amount not reached
                        removeCoupon();
                        return [
                            'status'    => false,
                            'message'   => localize('Minimum order amount is not reached to use this coupon')
                        ];
                    }
                } else {
                    # expired
                    removeCoupon();
                    return [
                        'status'    => false,
                        'message'   => localize('Coupon has been expired')
                    ];
                }
            } else {
                # coupon not found
                removeCoupon();
                return [
                    'status'    => false,
                    'message'   => localize('Coupon is not valid')
                ];
            }
        }

        // coupon not set - so return true
        return [
            'status'    => true,
            'message'   => ''
        ];
    }
}

if (!function_exists('getTotalTax')) {
    // get Total Tax from 
    function getTotalTax($carts)
    {
        $tax = 0;
        if ($carts) {

            foreach ($carts as $cart) {
                $product    = $cart->product_variation->product;
                $variation  = $cart->product_variation;

                $variationTaxAmount = variationTaxAmount($product, $variation);
                $tax += (float) $variationTaxAmount * $cart->qty;
            }
        }
        return $tax;
    }
}

if (!function_exists('getScheduledDeliveryType')) {
    // delivery type Status
    function getScheduledDeliveryType()
    {
        return "scheduled";
    }
}

if (!function_exists('paidPaymentStatus')) {
    // paid Payment Status
    function paidPaymentStatus()
    {
        return "paid";
    }
}
if (!function_exists('unpaidPaymentStatus')) {
    // unpaid Payment Status
    function unpaidPaymentStatus()
    {
        return "unpaid";
    }
}

if (!function_exists('orderPlacedStatus')) {
    // orderPlacedStatus
    function orderPlacedStatus()
    {
        return "order_placed";
    }
}
if (!function_exists('orderPendingStatus')) {
    // orderPendingStatus
    function orderPendingStatus()
    {
        return "pending";
    }
}
if (!function_exists('orderProcessingStatus')) {
    // orderProcessingStatus
    function orderProcessingStatus()
    {
        return "processing";
    }
}

// todo:: orderIsPickedUpStatus Order status
if (!function_exists('orderPickedUpStatus')) {
    // orderIsPickedUpStatus
    function orderPickedUpStatus()
    {
        return "picked_up";
    }
}

// todo:: OutForDelivery Order status
if (!function_exists('orderOutForDeliveryStatus')) {
    // orderProcessingStatus
    function orderOutForDeliveryStatus()
    {
        return "out_for_delivery";
    }
}

if (!function_exists('orderDeliveredStatus')) {
    // order Delivered Status 
    function orderDeliveredStatus()
    {
        return "delivered";
    }
}

if (!function_exists('orderCancelledStatus')) {
    // order cancelled Status 
    function orderCancelledStatus()
    {
        return "cancelled";
    }
}


if (!function_exists('checkCity')) {
    // price to usd
    function checkCity($city_id,$branch_id)
    {
        $city = \App\Models\BranchCity::where('city_id',$city_id)->where('branch_id',$branch_id)->first();
        if ($city){
            return true;
        }else{
            false ;
        }
    }

}
if (!function_exists('callApi')) {
    // price to usd
    function callAPI($method, $url, $data){
        $is_live = 'no';
        $use_proxy = 'no';
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data) $url = sprintf("%s?%s", $url, http_build_query($data));
        }// OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        //PROTOCOL_ERROR
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        if ($is_live === 'yes') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        }// PROXY
        if ($use_proxy === 'yes') {
            //$proxy = ‘your proxy’;curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }//EXECUTE
        $result = curl_exec($curl);
        if (curl_errno($curl)) {

            $error_msg = curl_error($curl);

        }

        if (isset($error_msg)) {

            echo "Web Exception Raised::::::::::::::::".$error_msg;

        }

        // if(!$result){
        //     die("Connection Failure");
        // }
        curl_close($curl);
        return $result;
    }



    if (!function_exists('addNewRecentlyViewProduct')) {
        // order cancelled Status
        function addNewRecentlyViewProduct($product_id)
        {

            $recentlyViewedProductIds = Session::get('recently_viewed_products', []);

            if (!in_array($product_id, $recentlyViewedProductIds)) {
                $recentlyViewedProductIds[] = $product_id;

                // Store the updated product IDs back in the session
                Session::put('recently_viewed_products', $recentlyViewedProductIds);
            }
        }
    }
    if (!function_exists('checkRefundItem')) {
        // order cancelled Status
        function checkRefundItem($item_id,$order_id)
        {
            $refundRequest = \App\Models\RefundRequest::where('order_id', $order_id)
                ->where('user_id', \Illuminate\Support\Facades\Auth::user()->id)
                ->whereHas('refundItems',function($q) use ($item_id) {
                    $q->where('order_item_id', $item_id);
                })
                ->first();
            if ($refundRequest){
                return "true";
            }else{
                return "false";
            }
        }
    }

    if (!function_exists('posPrice')) {
        // min/base price of a product
        function posPrice($product, $formatted = false)
        {
            $price = $product->min_price;

            $branch_id = \Illuminate\Support\Facades\Session::get('branch_id');
                if ($branch_id) {
                    $productPrice = \App\Models\ProductPrice::where('branch_id', $branch_id)
                        ->where('product_id', $product->id)
                        ->value('max_price');

                    if ($productPrice !== null) {
                        $price = $productPrice;
                    }
                }
            $tax = 0;

            foreach ($product->taxes as $productTax) {
                if ($productTax->tax_type == 'percent') {
                    $tax += ($price * $productTax->tax_value) / 100;
                } elseif ($productTax->tax_type == 'flat') {
                    $tax += $productTax->tax_value;
                }
            }
            $price += $tax;

            return $formatted ? formatPrice($price) : $price;
        }
    }


    if (!function_exists('checkReview')) {
        // order cancelled Status
        function checkReview($item_id)
        {
            $reviewProduct = \App\Models\Review::where('product_id',$item_id)->where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->first();
            if ($reviewProduct){
                return "true";
            }else{
                return "false";
            }
        }
    }

    if (!function_exists('checkReviewstatus')) {
        // order cancelled Status
        function checkReviewstatus($itemCount)
        {
            $reviewProduct = \App\Models\Review::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->first();
            if ($reviewProduct){
                return "true";
            }else{
                return "false";
            }
        }
    }

}

