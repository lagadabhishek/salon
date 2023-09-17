<?php
namespace App\Http\Services\Utility;

use Illuminate\Support\Facades\Log;
use App\Http\Services\Utility\ResponseUtility;

class HTTPUtil {

    //Customized method only for new GET call
    public static function HTTPGet($url) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseWithHeaderAndBody = array();
            $response = curl_exec($ch);
            $responseWithHeaderAndBody["response"] = $response != false ? $response : curl_error($ch);
            $responseWithHeaderAndBody['headerStatusCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $responseWithHeaderAndBody;
        } catch (Exception $e) {
            Log::info("getApiResponse-curlException" . PHP_EOL . $e->getMessage());
            return ResponseUtility::respondWithError($e->getCode(), $e->getMessage() . " in " . $e->getFile() . " on line number:" . $e->getLine());
        }
    }
    
    
    
    
    public static function httpPost($url, $data)
    {
        try{
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            $headers = array(
                "Content-Type:application/json"
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }catch (Exception $e){
            Log::info("getApiResponse-curlException" . PHP_EOL . $e->getMessage());
            return ResponseUtility::respondWithError($e->getCode(), $e->getMessage() . " in " . $e->getFile() . " on line number:" . $e->getLine());
        }
        
    }
    
    
    

}
