<!--COD-->
{{--@php--}}
{{--    $stringData='--}}

{{--    {--}}

{{--          "USER_ID": "esajeesadmin",--}}

{{--        "PASSWORD": "F@Syc3TtTP",--}}

{{--        "CLIENT_NAME": "esajeesadmin",--}}

{{--        "RETURN_URL": "http://127.0.0.1:8000/hbl/success",--}}

{{--        "CANCEL_URL": "http://localhost:8080/phptest/fail.php",--}}

{{--        "CHANNEL": "HBLPay_Esajees_Website",--}}

{{--        "TYPE_ID": "0",--}}

{{--     "ORDER": {--}}

{{--            "DISCOUNT_ON_TOTAL": "0",--}}

{{--            "SUBTOTAL": "4039.00",--}}

{{--            "OrderSummaryDescription": [{--}}

{{--                "ITEM_NAME": "Product 1",--}}

{{--                "QUANTITY": "1",--}}

{{--                "UNIT_PRICE": "599.99",--}}

{{--                "OLD_PRICE": "0",--}}

{{--                "CATEGORY": "Test Category",--}}

{{--                "SUB_CATEGORY": "Test Sub Category"--}}

{{--            }--}}

{{--            ]--}}

{{--        },--}}

{{--        "SHIPPING_DETAIL": {--}}

{{--            "NAME": "null",--}}

{{--            "ICON_PATH": null,--}}

{{--            "DELIEVERY_DAYS": "0",--}}

{{--            "SHIPPING_COST": "0"--}}

{{--        },--}}

{{--       "ADDITIONAL_DATA": {--}}

{{--            "REFERENCE_NUMBER": "80170",--}}

{{--            "CUSTOMER_ID": 123,--}}

{{--            "CURRENCY": "PKR",--}}

{{--            "BILL_TO_FORENAME": "John",--}}

{{--            "BILL_TO_SURNAME": "Don",--}}

{{--            "BILL_TO_EMAIL": "null@cybersource.com",--}}

{{--            "BILL_TO_PHONE": "1231231234",--}}

{{--            "BILL_TO_ADDRESS_LINE": "Test street",--}}

{{--            "BILL_TO_ADDRESS_CITY": "Karachi",--}}

{{--            "BILL_TO_ADDRESS_STATE": "SD",--}}

{{--            "BILL_TO_ADDRESS_COUNTRY": "PK",--}}

{{--            "BILL_TO_ADDRESS_POSTAL_CODE": "75400",--}}

{{--            "SHIP_TO_FORENAME": "John",--}}

{{--            "SHIP_TO_SURNAME": "Don",--}}

{{--            "SHIP_TO_EMAIL": "null@cybersource.com",--}}

{{--            "SHIP_TO_PHONE": "1231231234",--}}

{{--            "SHIP_TO_ADDRESS_LINE": "Test street",--}}

{{--            "SHIP_TO_ADDRESS_CITY": "Karachi",--}}

{{--            "SHIP_TO_ADDRESS_STATE": "SD",--}}

{{--            "SHIP_TO_ADDRESS_COUNTRY": "PK",--}}

{{--            "SHIP_TO_ADDRESS_POSTAL_CODE": "75400",--}}

{{--            "MerchantFields": {--}}

{{--                "MDD1": "WC",--}}

{{--                "MDD2": "YES",--}}

{{--                "MDD3": "Product Category",--}}

{{--                "MDD4": "Product Name",--}}

{{--                "MDD5": "No",--}}

{{--                "MDD6": "Standard",--}}

{{--                "MDD7": "1",--}}

{{--                "MDD8": "Pakistan",--}}

{{--                "MDD20": "NO"--}}

{{--            }--}}

{{--        }--}}

{{--    }--}}

{{--    ';--}}





{{--    class cyb{--}}


{{--             private $publicPEMKey = "-----BEGIN PUBLIC KEY-------}}
{{--MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA08NQsCLyONMq1t+nOUJL--}}
{{--1WSqpZ9+aTv5yY1+OvV6RexTa4CIwsgjASylm/woAuU53o0slD8+xRfVtHUImbFG--}}
{{--oqj00stj8aGU5J5Y76kgghl1L1vQi1E3zvpkwb5CkIH5eWRsL4HE6q5dhkJH6w1y--}}
{{--/5WFAvcsArlTnkWugR2VMN1cMVB3043ZmBYVTow9tkYyL3xHa/RUEIu8vcEeqkDx--}}
{{--o0/gYAG2i7+S/ZoIXxcx3peZ7FwZtt6o/YoDsBHh2rZL5hgUaQXG10Wm59llcHPU--}}
{{--jFFCN4VM2kimTcdhP6wVOQgoNherhP7s2q4wwLV9KuU8xQdZGdIfiOg00VzPTSpO--}}
{{--k/wIoSTasHQvr4zX+OrsqW0CprkaaDV1fHMiBErMZpnXNO4tSzXlFPjK0Z+7oUxJ--}}
{{--CugSIrpB3HGdv1Ec3+ZlsMj1KZcDKa6LQMyVsuSVdmRt9hY2di3tbP8vtTZUy/XU--}}
{{--WNAnsHPCoAFrdwZSnOkVMn0uY6pC5WCq+IHmFlmckqQoP4Wq+HxHVqDHpCyZRIdn--}}
{{--vjXMxNa5r+xTRlouPS3NfYzP+29RP9IAzEgS5bbM4OkQU6+XVY2Ws98A6XXlOXZe--}}
{{--gHLMUZbDFM9jkh9KdjRBjEdB0EBb7Nyt0LQL4dvVuPuwKdNsuMq1fzRyTekwKYVQ--}}
{{--E1wbGUYGA+cLXmxaQ+GtLbECAwEAAQ==--}}
{{-------END PUBLIC KEY-----";--}}


{{--        public function rsaEncryptCyb($plainData, $publicPEMKey=null){--}}

{{--            if(!$publicPEMKey)--}}

{{--                $publicPEMKey=$this->publicPEMKey;--}}



{{--            $encryptionOk = openssl_public_encrypt ($plainData, $encryptedData, $publicPEMKey, OPENSSL_PKCS1_PADDING);--}}



{{--            if($encryptionOk === false){--}}

{{--                return false;--}}

{{--            }--}}

{{--            return base64_encode($encryptedData);--}}



{{--            return false;--}}



{{--        }--}}



{{--        public function rsaDecryptCyb($data,$publicPEMKey=null)--}}

{{--        {--}}

{{--            //to do--}}

{{--        }--}}

{{--    }--}}

{{--    function recParamsEncryption($arrJson,$cyb){--}}

{{--        foreach($arrJson as $jsonIndex => $jsonValue){--}}

{{--            if( !is_array($jsonValue))--}}

{{--                if($jsonIndex!=="USER_ID")--}}

{{--                    $arrJson[$jsonIndex]=$cyb->rsaEncryptCyb($jsonValue);--}}

{{--                else--}}

{{--                    $arrJson[$jsonIndex]=$jsonValue;--}}

{{--            else{--}}

{{--                $arrJson[$jsonIndex]=recParamsEncryption($jsonValue,$cyb);--}}

{{--            }--}}

{{--        }--}}

{{--        return $arrJson;--}}

{{--    }--}}







{{--    // EXECUTED CODE--}}

{{--    $cyb=new cyb;--}}

{{--    //print_r($stringData) ;exit;--}}

{{--    $arrJson=json_decode($stringData,true);--}}

{{--    //print_r($arrJson);exit;--}}

{{--    $arrJson=json_encode(recParamsEncryption($arrJson,$cyb));--}}


{{--    $url="https://testpaymentapi.hbl.com/hblpay/api/checkout";--}}

{{--//debug(callAPI("POST",$url,$cyb->encrypt_RSA($stringData)));--}}

{{--$jsonCyberSourceResult=json_decode(callAPI("POST",$url,$arrJson),true);--}}
{{--//dd($jsonCyberSourceResult);--}}

{{--@endphp--}}



@if (getSetting('enable_cod') == 1)
    <div class="checkout-radio d-flex align-items-center justify-content-between gap-3 bg-white rounded p-4 mt-3">
        <div class="radio-left d-inline-flex align-items-center">
            <div class="theme-radio">
                <input type="radio" name="payment_method" id="cod" value="cod" required>
                <span class="custom-radio"></span>
            </div>
            <label for="cod" class="ms-2 h6 mb-0">{{ localize('Cash on delivery') }}
                ({{ localize('COD') }})</label>
        </div>
        <div class="radio-right text-end">
            <img src="{{ staticAsset('frontend/pg/cod.svg') }}" alt="cod" class="img-fluid">
        </div>
    </div>
@endif

{{--@if($jsonCyberSourceResult["IsSuccess"] && $jsonCyberSourceResult["ResponseMessage"]=="Success" && $jsonCyberSourceResult["ResponseCode"]==0)--}}
{{--    @php--}}
{{--        $sessionId=base64_encode($jsonCyberSourceResult["Data"]["SESSION_ID"]);--}}
{{--    @endphp--}}
    <input type="hidden" name="hbl_redirect" value="https://testpaymentapi.hbl.com/HBLPay/Site/index.html#/checkout?data=">

    <div class="checkout-radio d-flex align-items-center justify-content-between gap-3 bg-white rounded p-4 mt-3">
        <div class="radio-left d-inline-flex align-items-center">
            <div class="theme-radio">
                <input type="radio" name="payment_method" id="hbl_pay" value="hbl_pay" required>
                <span class="custom-radio"></span>
            </div>
            <label for="hbl_pay" class="ms-2 h6 mb-0">{{ localize('HBL Pay') }}
                ( Online Pay )</label>
        </div>
        <div class="radio-right text-end">
            <img src="{{ staticAsset('frontend/pg/cod.svg') }}" alt="cod" class="img-fluid">
        </div>
    </div>
{{--@endif--}}


<!--Paypal-->
@if (getSetting('enable_paypal') == 1)
    <div class="checkout-radio d-flex align-items-center justify-content-between gap-3 bg-white rounded p-4 mt-3">
        <div class="radio-left d-inline-flex align-items-center">
            <div class="theme-radio">
                <input type="radio" name="payment_method" id="paypal" value="paypal" required>
                <span class="custom-radio"></span>
            </div>
            <label for="paypal" class="ms-2 h6 mb-0">{{ localize('Pay with Paypal') }}</label>
        </div>
        <div class="radio-right text-end">
            <img src="{{ staticAsset('frontend/pg/paypal.svg') }}" alt="paypal" class="img-fluid">
        </div>
    </div>
@endif

<!--Stripe-->
@if (getSetting('enable_stripe') == 1)
    <div class="checkout-radio d-flex align-items-center justify-content-between gap-3 bg-white rounded p-4 mt-3">
        <div class="radio-left d-inline-flex align-items-center">
            <div class="theme-radio">
                <input type="radio" name="payment_method" id="stripe" value="stripe" required>
                <span class="custom-radio"></span>
            </div>
            <label for="stripe" class="ms-2 h6 mb-0">{{ localize('Pay with Stripe') }}</label>
        </div>
        <div class="radio-right text-end">
            <img src="{{ staticAsset('frontend/pg/stripe.svg') }}" alt="stripe" class="img-fluid">
        </div>
    </div>
@endif

<!--PayTm-->
@if (getSetting('enable_paytm') == 1)
    <div class="checkout-radio d-flex align-items-center justify-content-between gap-3 bg-white rounded p-4 mt-3">
        <div class="radio-left d-inline-flex align-items-center">
            <div class="theme-radio">
                <input type="radio" name="payment_method" id="paytm" value="paytm" required>
                <span class="custom-radio"></span>
            </div>
            <label for="paytm" class="ms-2 h6 mb-0">{{ localize('Pay with PayTm') }}</label>
        </div>
        <div class="radio-right text-end">
            <img src="{{ staticAsset('frontend/pg/paytm.svg') }}" alt="paytm" class="img-fluid">
        </div>
    </div>
@endif

<!--Razorpay-->
@if (getSetting('enable_razorpay') == 1)
    <div class="checkout-radio d-flex align-items-center justify-content-between gap-3 bg-white rounded p-4 mt-3">
        <div class="radio-left d-inline-flex align-items-center">
            <div class="theme-radio">
                <input type="radio" name="payment_method" id="razorpay" value="razorpay" required>
                <span class="custom-radio"></span>
            </div>
            <label for="razorpay" class="ms-2 h6 mb-0">{{ localize('Pay with Razorpay') }}</label>
        </div>
        <div class="radio-right text-end">
            <img src="{{ staticAsset('frontend/pg/razorpay.svg') }}" alt="razorpay" class="img-fluid">
        </div>
    </div>
@endif
