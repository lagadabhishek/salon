<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Utility\PostmarkService;


class Users extends Model
{
    use HasFactory;

    public static function CreateUsers($insertData) 
    {
        try {
            $lastInsertedID = DB::table('tbl_users')->insertGetId($insertData);
            Log::info("User registered successfully. [" . $insertData['usr_email'] . ": " . json_encode($insertData) ."]");
            return $lastInsertedID;
        } catch (Exception $e) {
            Log::info("Got exception in create user " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function checkUserCredentials($payload, $pass)
    {
        try {
            $userData = DB::table('tbl_users')
                    ->select('usr_password', 'usr_id')
                    ->where('usr_email', $payload['email'])
                    ->first();

            $returnData = array();
            if (isset($userData) && !empty($userData)) 
            {
                $stored_hash = $userData->usr_password;
                $usr_id = $userData->usr_id;

                if (password_verify($pass, $stored_hash))
                {
                    $query = DB::table('tbl_users')
                        ->select('usr_id', 'usr_email', 'usr_first_name', 'usr_last_name', 'usr_phone_number', 'usr_role_id')
                        ->where('usr_email', $payload['email'])
                        ->where('usr_id', $usr_id)
                        ->first();

                    $returnData['data'] = (array) $query;
                }
            }
            return $returnData;
        } catch (Exception $e) {
            Log::info("LeaderboardModel: Got exception in checkUserCredentials function ".PHP_EOL.$e->getMessage());
        }
    }

    public static function createUserSession($insertData) 
    {
        try {
            $lastInsertedID = DB::table('tbl_session_tokens')->insertGetId($insertData);
            Log::info("User login successfully. [" . $insertData['usr_email'] . "]");
            return $lastInsertedID;
        } catch (Exception $e) {
            Log::info("Got exception in create user session " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function updateLastLoginDetails($userID, $ipAddress) 
    {
        try {
            $updateData = DB::table('tbl_users')
                    ->where('usr_id', $userID)
                    ->update(array('usr_last_ip' => $ipAddress, 'usr_last_login' => time()));
            return $updateData;
        } catch (Exception $e) {
            Log::info("Got exception in update last login details " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function checkEmailExists($email) 
    {
        try {
            $count = DB::table('tbl_users')
                    ->where('usr_email', $email)
                    ->count();
            if ($count > 0) {
                Log::info("User email is already exists. [" . $email ."]");
            }
            return $count;
        } catch (Exception $e) {
            Log::info("Got exception in checkEmailExists " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function getUserDataByToken($sessionToken) 
    {
        try {
            $fourHr = time() - (3600*4);
            $sessionTokenData = DB::table('tbl_session_tokens')
                    ->select('usr_id', 'usr_email as email', 'usr_first_name as first_name', 'usr_last_name as last_name', 'usr_phone_number as phone_number', 'usr_role_id as role_id')
                    ->where('session_token', $sessionToken)
                    ->where('created_on', '>=', $fourHr)
                    ->first();
            return $sessionTokenData;
        } catch (Exception $e) {
            Log::info("Got exception in get user data by token " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function forgotPassword($email) 
    {
        try {
            $postmark = new PostmarkService();
            $query = DB::table('tbl_users')
                    ->select('usr_email','usr_id')
                    ->where('usr_email', $email)
                    ->first();

            if (!isset($query) && empty($query))
                return false;

            $usrId = $query->usr_id;
            $rootUrl = getenv('HAIR_SALON_ROOT_URL');
            $token = md5(rand().microtime());

            $insertData['usr_id']  = $usrId;
            $insertData['email']   = $email;
            $insertData['token']   = $token;
            $insertData['date_created'] = time();

            $lastInsertedID = DB::table('tbl_forgot_password')->insertGetId($insertData);
            Log::info("Forgot password details inserted successfully [" . $email ."]");

            $reset_link = $rootUrl.'auth/reset-password/'.$email.'/'.$token;

            $reset_content = '<p>Dear Hair Salon Book Appointment User, <br /><br />'.
            'You have requested a reset password for your Hair Salon Book Appointment account.'.
            '<br><br>Just click on the link below or copy and paste it into your web browser <br> <a href="'.$reset_link.'">'.$reset_link.'</a>'. 
            '<br><br><p> <br /><p>Thanks,<br />Hair Salon Book Appointment<br /></p>';

            $postmark->to($email);
            $postmark->subject('Reset Password Instructions');
            $postmark->message_html($reset_content);  
            $postmark->send();

            Log::info("Sent email with reset password instructions [" . $email ."]");
            return true;
        } catch (Exception $e) {
            Log::info("Got exception in forgot password " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

    public static function validateForgotLink($payload)
    { 
        try {
            $query =  DB::table('tbl_forgot_password')
                     ->select('email','token','is_link_used')
                     ->where(array("email" => $payload['email'], "token" => $payload['token']))
                     ->get();
            
            $count = count($query); 

            if($count != 1)
                return false;

            $isLinkValid = $query[0]->is_link_used;
            if($isLinkValid == 1)
                return 1;
            else
                return 2;

        } catch (Exception $e) {
            Log::info("Got exception in validate forgot link " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }             
    }

    public static function resetPassword($payload, $pass)
    { 
        try {
            $validToken = DB::table('tbl_forgot_password')
                    ->where('token', $payload['token'])
                    ->where('email', $payload['email'])
                    ->count();

            if ($validToken == 0)
                return false;

            $updatePass =  DB::table('tbl_users')
                    ->where('usr_email', $payload['email'])
                    ->update(array("usr_password" => $pass));

            $updateLinkUsed =  DB::table('tbl_forgot_password')
                    ->where('token', $payload['token'])
                    ->update(array("is_link_used" => '0'));

            Log::info('User password reset successfully for user. [' .$payload['email']. ']');
            return true;
        } catch (Exception $e) {
            Log::info("Got exception in reset password " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }            
    }

    public static function validateSessionToken($sessionToken)
    {
        try {
            $fourHr = time() - (3600*4);
            $count = DB::table('tbl_session_tokens')
                ->where('session_token', $sessionToken)
                ->where('created_on', '>=', $fourHr)
                ->count();

            if ($count > 0)
            { 
                $updateData = DB::table('tbl_session_tokens')
                        ->where('session_token', $sessionToken)
                        ->update(array('created_on' => time()));
                return true;
            }else
            {
                $deleteToken = DB::table('tbl_session_tokens')
                        ->where('created_on', '<=', $fourHr)
                        ->delete();
                return false;
            }

        } catch (Exception $e) {
            Log::info("Got exception in check valid session token " . __METHOD__ . " " . PHP_EOL . $e->getMessage());
        }
    }

}
