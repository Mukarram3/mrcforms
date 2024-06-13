<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HblPayController extends Controller
{
    public function view(){
        return view('frontend.default.pages.hblpay.index');
    }

    public function callAPI($method, $url, $data){
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

    public function debug($mixParam, $bolToStop = false){
        if (defined("DEBUG_MODE") && DEBUG_MODE == "1") {
            $arrDebugParams = debug_backtrace();
            //print_r($arrDebugParams);

            echo '<pre style="background-color:#F90; border:#000 thin solid; font-family:Verdana, Geneva, sans-serif;padding:3px;">';
            echo 'Value of variable at <i>' . $arrDebugParams[0]['file'] . ':' . $arrDebugParams[0]['line'] . '</i> is <br />';
            if (empty($mixParam))
                echo '<i><b>empty</b></i>';
            else
                print_r($mixParam);
            echo '</pre>';


            //echo $strOutputHtml;
            if ($bolToStop)
                exit;
        }
    }

}
