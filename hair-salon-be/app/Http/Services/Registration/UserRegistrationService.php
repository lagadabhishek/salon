<?php

namespace App\Http\Services\Registration;

use App\Models\Users;
use Illuminate\Support\Facades\Log;

class UserRegistrationService 
{
    public function CreateUsers($payload) 
    {
        $majorsalt = env('AUTH_SALT', '');
        $pass = password_hash($this->_encode($payload['password'], $majorsalt), PASSWORD_BCRYPT);

        $userData = array(
                        'usr_first_name' => $payload['first_name'],
                        'usr_last_name'  => $payload['last_name'],
                        'usr_email'      => $payload['email'],
                        'usr_password'   => $pass,
                        'usr_phone_number' => $payload['phone_number'],
                        'usr_gender'     => $payload['gender'],
                        'usr_last_ip'    => $payload['ip_address'],
                        'usr_last_login' => time(),
                        'usr_created_on' => time()
                    );

        return Users::CreateUsers($userData);
    }

    public function _encode($password, $majorsalt)
    {
        if (function_exists('str_split'))
        {
            $_pass = str_split($password);
        }

        // encrypts every single letter of the password
        foreach ($_pass as $_hashpass)
        {
            $majorsalt .= md5($_hashpass);
        }
        return md5($majorsalt);
    }

    public function getIp()
    {   
        $rules = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($rules as $key)
        {
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }

    public function userLogin($payload)
    {
        $majorsalt = env('AUTH_SALT', '');

        $pass = $this->_encode($payload['password'], $majorsalt);
        $userData = Users::checkUserCredentials($payload, $pass);
        $count = count($userData);
        if($count > 0)
            return $this->createUserSession($userData, $payload['ip_address']);
        else
            return false;      
    }

    public function createUserSession($accountDetails, $ipAddress) 
    {   
        $token = null;
        $token = str_shuffle(md5(date("hh-mm-yyyy-H:i:s")));

        $sessionData = array(
                            'usr_id' => $accountDetails['data']['usr_id'],
                            'usr_role_id' => $accountDetails['data']['usr_role_id'],
                            'session_token' => $token,
                            'usr_email' => $accountDetails['data']['usr_email'],
                            'usr_first_name' => $accountDetails['data']['usr_first_name'],
                            'usr_last_name' => $accountDetails['data']['usr_last_name'],
                            'usr_phone_number' => $accountDetails['data']['usr_phone_number'],
                            'created_on' => time());

        Users::createUserSession($sessionData);
        Users::updateLastLoginDetails($accountDetails['data']['usr_id'], $ipAddress);

        return array("session_token" => $token, "user_data" => $accountDetails);
    }

    public function checkEmailExists($email)
    {
        return Users::checkEmailExists($email);
    }

    public function getUserDataByToken($sessionToken)
    {
        return Users::getUserDataByToken($sessionToken);
    }

    public function forgotPassword($email)
    {
        return Users::forgotPassword($email);
    }

    public function validateForgotLink($payload)
    {
        return Users::validateForgotLink($payload);
    }

    public function resetPassword($payload)
    {
        $majorsalt = env('AUTH_SALT', '');
        $pass = password_hash($this->_encode($payload['new_password'], $majorsalt), PASSWORD_BCRYPT);
        return Users::resetPassword($payload, $pass);
    }

    public function validateSessionToken($sessionToken)
    {
        return Users::validateSessionToken($sessionToken);
    }
}
