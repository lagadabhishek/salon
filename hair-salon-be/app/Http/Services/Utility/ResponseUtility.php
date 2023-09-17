<?php

namespace App\Http\Services\Utility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Illuminate\Http\Response as Resp;
use Response;

class ResponseUtility 
{
    protected static $statusCode = Resp::HTTP_OK;

    public static function respondNotFound($message = 'Not Found!') {
        ResponseUtility::$statusCode = Resp::HTTP_NOT_FOUND;
        return $this->respond([
                    'success' => false,
                    'message' => $message,
        ]);
    }

    public static function respondInternalError($message) {
        ResponseUtility::$statusCode = Resp::HTTP_INTERNAL_SERVER_ERROR;
        return $this->respond([
                    'success' => false,
                    'message' => $message,
        ]);
    }

    public static function respondValidationError($message, $errors) {
        ResponseUtility::$statusCode = Resp::HTTP_OK;
        return ResponseUtility::respond([
                    'success' => false,
                    'message' => $message,
                    'data' => $errors
        ]);
    }

    public static function respond($data, $headers = []) {
         return  Response::json($data, ResponseUtility::$statusCode, $headers);
         
    }

    public static function respondWithError($errorCode, $data = null) {
        ResponseUtility::$statusCode = Resp::HTTP_OK;
        return ResponseUtility::respond([
                    'success' => false,
                    'message' => trans('error.'.$errorCode),
                    'message_code' => $errorCode,
                    'data' => $data
        ]);
    }
    public static function respondWithSuccess($successCode, $data = null) {
        ResponseUtility::$statusCode = Resp::HTTP_OK;
        return ResponseUtility::respond([
                    'success' => true,
                    'message' => trans('success.'.$successCode),
                    'message_code' => $successCode,
                    'data' => $data
        ]);
    }
    
     public static function serviceRespondWithError($errorCode, $errorMessage, $data = null) {
        ResponseUtility::$statusCode = Resp::HTTP_OK;
        return ResponseUtility::respond([
                    'success' => false,
                    'message' => $errorMessage,
                    'message_code' => $errorCode,
                    'data' => $data
        ]);
    }
    public static function respondUnAuthorized($errorCode, $data = null) {
        ResponseUtility::$statusCode = 401;
        return ResponseUtility::respond([
                    'success' => false,
                    'message' => trans('error.'.$errorCode),
                    'message_code' => $errorCode,
                    'data' => $data
        ]);
    }

}
