<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\Registration\UserRegistrationService;
use App\Http\Services\Utility\ResponseUtility;
use Validator;

class UserController extends Controller
{
     /**
     * @OA\Post(
     * path="/api/user/add_user",
     * summary="User registration",
      *   description="Register user with basic details<br/>
       Success Code:<br/>
       4501: User registered successfully.<br/>
       Error Code:<br/>
       4001: Please enter a valid firstname.<br/>
       4002: Please enter a valid lastname.<br/>
       4003: Please enter a valid email.<br/>
       4004: Please enter a valid password.<br/>
       4005: Please enter a valid phone number.<br/>
       4006: Please enter a valid gender.<br/>
       4007: This email address is already exists.<br/>
       ",
     * tags={"User Registration"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"first_name","last_name", "email", "password", "phone_number", "gender"},
     *       @OA\Property(property="first_name", type="string", example="John"),
     *       @OA\Property(property="last_name", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", format="email", example="john@doe.com"),
     *       @OA\Property(property="phone_number", type="string", example="1526354587"),
     *       @OA\Property(property="password", type="string", example="1234536"),
     *       @OA\Property(property="gender", type="string", example="MALE/FEMALE/OTHER"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */


    public function CreateUsers(Request $request)
    {
        $payload = $request->all();
        $userRegistrationService = new UserRegistrationService();

        $firstNameRules   = ['first_name' => 'required'];
        $lastNamesRules   = ['last_name'  => 'required'];
        $emailRules       = ['email'      => 'required|email'];
        $passwordRules    = ['password'   => 'required'];
        $phoneNumberRules = ['phone_number' => 'required'];
        $genderRules      = ['gender' => 'required'];

        //first_name validation...
        $validFirstName = Validator::make($request->all(), $firstNameRules);
        if ($validFirstName->fails()) {
            return ResponseUtility::respondWithError(4001, null);
        }
        //last_name validation...
        $validLastName = Validator::make($request->all(), $lastNamesRules);
        if ($validLastName->fails()) {
            return ResponseUtility::respondWithError(4002, null);
        }
        //email validation...
        $validEmail = Validator::make($request->all(), $emailRules);
        if ($validEmail->fails()) {
            return ResponseUtility::respondWithError(4003, null);
        }
        //password validation...
        $validPassword = Validator::make($request->all(), $passwordRules);
        if ($validPassword->fails()) {
            return ResponseUtility::respondWithError(4004, null);
        }
        //phone number validation...
        $validPhoneNumber= Validator::make($request->all(), $phoneNumberRules);
        if ($validPhoneNumber->fails()) {
            return ResponseUtility::respondWithError(4005, null);
        }
        //Gender validation...
        $validGender= Validator::make($request->all(), $genderRules);
        if ($validGender->fails()) {
            return ResponseUtility::respondWithError(4006, null);
        }

        //Dynamic validations...
        $validEmailCount = $userRegistrationService->checkEmailExists($payload['email']);
        if($validEmailCount > 0){
           return ResponseUtility::respondWithError(4007, array("email" => $payload['email']));
        }
        
        $payload['ip_address'] = $userRegistrationService->getIp();
        $usrID = $userRegistrationService->CreateUsers($payload);
        
        //GENERATE users session-token...
        $sessionData = $userRegistrationService->userLogin($payload);
        return ResponseUtility::respondWithSuccess(4501, array("user_id" => $usrID, "session_token" => $sessionData['session_token']));
    }

    /**
     * @OA\Post(
     * path="/api/users/login",
     * summary="User Login",
      *   description="Login user<br/>
        Success Code:<br/>
            4502: User login successfully.<br/>
        Error Code:<br/>
            4003: Please enter valid email.<br/>
            4004: Please enter valid password.<br/>
            4008: Please enter valid credentials.<br/>
       ",
     * tags={"User Registration"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", example="john@doe.com"),
     *       @OA\Property(property="password", type="string", example="1234536"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response"
     *     )
     * )
     */
    public function loginAccount(Request $request) 
    {
        $payload = $request->all();

        $emailRules    = ['email' => 'required|email'];
        $passwordRules = ['password' => 'required'];

        //Email validation...
        $validEmail = Validator::make($request->all(), $emailRules);
        if ($validEmail->fails()) {
            return ResponseUtility::respondWithError(4003, null);
        }
        //Password validation...
        $validPassword = Validator::make($request->all(), $passwordRules);
        if ($validPassword->fails()) {
            return ResponseUtility::respondWithError(4004, null);
        }

        $userRegistrationService = new UserRegistrationService();
        $payload['ip_address'] = $userRegistrationService->getIp();
        $userData = $userRegistrationService->userLogin($payload);
        if($userData == ""){
            return ResponseUtility::respondWithError(4008, null);
        }
        return ResponseUtility::respondWithSuccess(4502, 
            array('session_token' => $userData['session_token'], 
                  'user_data' => $userData['user_data']['data']));
    }

    /**
     * @OA\Get(
     * path="/api/users/validate_session_token",
     * summary="User validate session token.",
      *   description="User validate session token<br/>
        Success Code:<br/>
            4503: User details fetched successfully.<br/>
        Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
       ",
     * tags={"User Registration"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response"
     *     )
     * )
     */
    public function validateSessionToken(Request $request) 
    {
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();

        //Take care about DB
        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        if (isset($userData))
            return ResponseUtility::respondWithSuccess(4503, $userData);
        else
            return ResponseUtility::respondWithError(40001, null);
    }

    /**
     * @OA\Post(
     * path="/api/users/forgot_password",
     * summary="User forgot password.",
      *   description="User forgot password<br/>
        Success Code:<br/>
            4504: We just sent you an email with password reset instructions.<br/>
        Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
            4003: Please enter valid email.<br/>
            4009: This email address does not exists, Please check it.<br/>
       ",
     * tags={"User Registration"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", example="john@doe.com"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response"
     *     )
     * )
     */
    public function forgotPassword(Request $request) 
    {
        $payload = $request->all();
        $userRegistrationService = new UserRegistrationService();

        $emailRules = ['email' => 'required|email'];

        //Email validation...
        $validEmail = Validator::make($request->all(), $emailRules);
        if ($validEmail->fails()) {
            return ResponseUtility::respondWithError(4003, null);
        }

        $forgotPasswordLink = $userRegistrationService->forgotPassword($payload['email']);
        if (!$forgotPasswordLink)
            return ResponseUtility::respondWithError(4009, array('email' => $payload['email']));
        else
            return ResponseUtility::respondWithSuccess(4504, null);
    }

    /**
     * @OA\Post(
     * path="/api/users/validate_forgot_link",
     * summary="validate forgot link",
      *   description="Validate forgot link<br/>
        Success Code:<br/>
            4505: User has been validated successfully.<br/>
        Error Code:<br/>
            4003: Please enter valid email.<br/>
            4010: Please enter valid token.<br/>
            4011: Please enter valid email Id and token.<br/>
            4012: You have already used this link, Please create new link.<br/>
       ",
     * tags={"User Registration"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","token"},
     *       @OA\Property(property="email", type="string", example="john@gmail.com"),
     *       @OA\Property(property="token", type="string", example="af615ead869f01ea38b22e080f91142f"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response"
     *     )
     * )
     */
    public function validateForgotLink(Request $request) 
    {
        $payload = $request->all();
        $emailRules = ['email' => 'required'];
        $tokenRules = ['token' => 'required'];

        //Email id validation...
        $validEmailID = Validator::make($request->all(), $emailRules);
        if ($validEmailID->fails()) {
            return ResponseUtility::respondWithError(4003, null);
        }

        //token validation...
        $validToken = Validator::make($request->all(), $tokenRules);
        if ($validToken->fails()) {
            return ResponseUtility::respondWithError(4010, null);
        }

        $userRegistrationService = new UserRegistrationService();
        $forgotPasswordLink = $userRegistrationService->validateForgotLink($payload);
        if(! $forgotPasswordLink){
            return ResponseUtility::respondWithError(4011, null);         
        }elseif($forgotPasswordLink == 2){
            return ResponseUtility::respondWithError(4012, null);
        }      
        
        return ResponseUtility::respondWithSuccess(4505, null);
    }

    /**
     * @OA\Post(
     * path="/api/users/reset_password",
     * summary="reset password",
      *   description="Reset Password<br/>
        Success Code:<br/>
            4506: Password has been reset successfully.<br/>
        Error Code:<br/>
            4003: Please enter valid email.<br/>
            4010: Please enter valid token.<br/>
            4011: Please enter valid email Id and token.<br/>
            4013: Please enter a valid new password.<br/>
       ",
     * tags={"User Registration"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"new_password","email","token"},
     *       @OA\Property(property="new_password", type="string", example="john@123"),
     *       @OA\Property(property="email", type="string", example="john@gmail.com"),
     *       @OA\Property(property="token", type="string", example="af615ead869f01ea38b22e080f91142f"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response"
     *     )
     * )
     */
    public function resetPassword(Request $request) 
    {
        $payload = $request->all();
        $passwordRules = ['new_password' => 'required'];
        $emailRules    = ['email' => 'required|email'];
        $tokenRules    = ['token' => 'required'];

        //new password validation...
        $validPassword = Validator::make($request->all(), $passwordRules);
        if ($validPassword->fails()) {
            return ResponseUtility::respondWithError(4013, null);
        }

        //Email id validation...
        $validEmailID = Validator::make($request->all(), $emailRules);
        if ($validEmailID->fails()) {
            return ResponseUtility::respondWithError(4003, null);
        }

        //token validation...
        $validToken = Validator::make($request->all(), $tokenRules);
        if ($validToken->fails()) {
            return ResponseUtility::respondWithError(4010, null);
        }

        $userRegistrationService = new UserRegistrationService();
        $resetPassword = $userRegistrationService->resetPassword($payload);
        if (! $resetPassword) 
            return ResponseUtility::respondWithError(4011, null); 
        else
            return ResponseUtility::respondWithSuccess(4506, null);
    }
}
