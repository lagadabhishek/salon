<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\Partner\PartnerServicesService; 
use App\Http\Services\Registration\UserRegistrationService;
use App\Http\Services\Utility\ResponseUtility;
use Validator;

class PartnerServiceController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/partner/service/add",
     * summary="Add service.",
      *   description="Add service codes.<br/>
       Success Code:<br/>
       4507: Service created successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4014: Please enter a valid name.<br/>
       4015: Please enter a valid discription.<br/>
       4016: Please enter a valid gender.<br/>
       4017: This Service name already exists.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name","discription", "gender"},
     *       @OA\Property(property="name", type="string", example="Skin/Hair"),
     *       @OA\Property(property="discription", type="string", example="Lorem Ipsum is simply dummy text"),
     *       @OA\Property(property="gender", type="string", format="email", example="Male/Female/Both"),
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

    public function createService(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();
        
        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $nameRules        = ['name' => 'required'];
        $discriptionRules = ['discription' => 'required'];
        $genderRules      = ['gender' => 'required'];

        //name validation...
        $validName = Validator::make($request->all(), $nameRules);
        if ($validName->fails())
            return ResponseUtility::respondWithError(4014, null);

        //Discription validation...
        $validDiscription = Validator::make($request->all(), $discriptionRules);
        if ($validDiscription->fails())
            return ResponseUtility::respondWithError(4015, null);

        //Gender validation...
        $validGender = Validator::make($request->all(), $genderRules);
        if ($validGender->fails())
            return ResponseUtility::respondWithError(4016, null);

        //Dyanamic Name Validation..
        $isName = $partnerService->isNameExists($payload['name'], $payload['account_id']);
        if($isName)
            return ResponseUtility::respondWithError(4017, null);

        $lastInsertedID = $partnerService->createService($payload);
        return ResponseUtility::respondWithSuccess(4507, $lastInsertedID);
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/list_all_service",
     * summary="List all Services.",
     *   description="List all Services.<br/>
      Success Code:<br/>
            4508: Services list fetched successfully.<br/>
       Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
       ",
     * tags={"Partner Management"},
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
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */
    public function listAllService(Request $request)
    {  
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        
        //Actual take care of DB
        $serviceList = $partnerService->listAllService($payload);
        return ResponseUtility::respondWithSuccess(4508, $serviceList);
    }
    
    /**
     * @OA\Get(
     * path="/api/partner/service/get_details",
     * summary="Service details.",
     *   description="Service details codes.<br/>
      Success Code:<br/>
            4509: Service details fetched successfully.<br/>
       Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
            4018: Please enter a valid service id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="service_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function getServiceDetails(Request $request) 
    {
        
        $payload = $request->all();
        
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;
        
        $idRules = ['service_id' => 'required|numeric'];

        //Service id validation...
        $validId = Validator::make($request->all(), $idRules);
        if ($validId->fails())
            return ResponseUtility::respondWithError(4018, null);

        //Dynamic service id validation..
        $count = $partnerService->countServiceID($payload['account_id'], $payload['service_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4018, $payload['service_id']);

        //Actual take care of DB
        $getServiceDetails = $partnerService->getServiceDetails($payload);
        
        return ResponseUtility::respondWithSuccess(4509, $getServiceDetails);
    }

    /**
     * @OA\Post(
     * path="/api/partner/service/update",
     * summary="Edit service.",
    *   description="Edit service codes.<br/>
       Success Code:<br/>
       4510: Service updated successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4014: Please enter a valid name.<br/>
       4015: Please enter a valid discription.<br/>
       4016: Please enter a valid gender.<br/>
       4017: This Service name already exists.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"service_id", "name", "discription", "gender"},
     *       @OA\Property(property="service_id", type="numeric", example="1/2"),
     *       @OA\Property(property="name", type="string", example="Skin/Hair"),
     *       @OA\Property(property="status", type="numeric", example="0/1"),
     *       @OA\Property(property="discription", type="string", example="Lorem Ipsum is simply dummy text"),
     *       @OA\Property(property="gender", type="string", format="email", example="Male/Female/Both"),
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

    public function updateService(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;
    
        $nameRules        = ['name' => 'required'];
        $discriptionRules = ['discription' => 'required'];
        $genderRules      = ['gender' => 'required'];
        $idRules          = ['service_id' => 'required'];

        //name validation...
        $validName = Validator::make($request->all(), $nameRules);
        if ($validName->fails())
            return ResponseUtility::respondWithError(4014, null);

        //Discription validation...
        $validDiscription = Validator::make($request->all(), $discriptionRules);
        if ($validDiscription->fails())
            return ResponseUtility::respondWithError(4015, null);

        //Gender validation...
        $validGender = Validator::make($request->all(), $genderRules);
        if ($validGender->fails())
            return ResponseUtility::respondWithError(4016, null);

        //Service id validation...
        $validId = Validator::make($request->all(), $idRules);
        if ($validId->fails())
            return ResponseUtility::respondWithError(4018, null);

        //Dynamic service id validation..
        $count = $partnerService->countServiceID($payload['account_id'], $payload['service_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4018, $payload['service_id']);

        //Dyanamic Name Validation..
        $isName = $partnerService->countName($payload['name'], $payload['account_id'], $payload['service_id']);
        if($isName)
            return ResponseUtility::respondWithError(4017, null);

        $update = $partnerService->updateService($payload);
        return ResponseUtility::respondWithSuccess(4510, $update);
    }

     /**
     * @OA\Post(
     * path="/api/partner/service/status_update",
     * summary="Status update service.",
      *   description="Status service codes.<br/>
       Success Code:<br/>
       4523: Service status update successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4018: Please enter a valid service id.<br/>
       4034: Please enter a status.</br>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"service_id","status"},
     *       @OA\Property(property="service_id", type="string", example="1/2"),
     *       @OA\Property(property="status", type="string", example="0/1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, Please try again")
     *        )
     *     )
     * )
     */

    public function serviceStatusUpdate(Request $request)
    {
        $payload = $request->all();

        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $serviceID = ['service_id' => 'required|numeric'];
        $status    = ['status'     =>  'required'];

        //serviceID validation
        $serviceIDValidation = Validator::make($request->all(),$serviceID);
        if ($serviceIDValidation->fails()) {
            return ResponseUtility::respondWithError(4018, null);
        }

        //status Validation..
        $statusValidation = Validator::make($request->all(), $status);
        if ($statusValidation->fails()) {
            return ResponseUtility::respondWithError(4034 ,null);
        }

        // Dynamic validation service id.. 
        $count = $partnerService->countServiceID($payload['account_id'], $payload['service_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4018, $payload['service_id']);


        $statusUpdate = $partnerService->serviceStatusUpdate($payload);
        return ResponseUtility::respondWithSuccess(4523,$statusUpdate);

    }

     /**
     * @OA\Delete(
     * path="/api/partner/service/delete",
     * summary="Delete service",
     *   description="Delete service.<br/>
      Success Code:<br/>
            4520: Service deleted successfully.<br/>
      Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            4018: Please enter a valid service id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="service_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, Please try again")
     *        )
     *     )
     * )
     */

     public function deleteService(Request $request)
     {
        $payload = $request->all();

        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;
        
        $serviceID = ['service_id' => 'required|numeric'];

        $serviceIDValidation = Validator::make($request->all(), $serviceID);
        if ($serviceIDValidation->fails()) {
           return ResponseUtility::respondWithError(4018, null);
        }

        //Dynamic validation service id.. 
        $count = $partnerService->countServiceID($payload['account_id'], $payload['service_id']);
        if ($count == 0){
            return ResponseUtility::respondWithError(4018, $payload['service_id']);
        }


        $delete = $partnerService->deleteService($payload);
        return ResponseUtility::respondWithSuccess(4520 , $delete);
     }

    /**
     * @OA\Post(
     * path="/api/partner/service/category/add",
     * summary="Add service category.",
      *   description="Add service category codes.<br/>
       Success Code:<br/>
       4511: Category created successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4014: Please enter a valid name.<br/>
       4015: Please enter a valid discription.<br/>
       4016: Please enter a valid gender.<br/>
       4018: Please enter a valid service id.<br/>
       4019: This Category name already exists.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"service_id","name","discription", "gender"},
     *       @OA\Property(property="name", type="string", example="Skin_care/Hair_care"),
     *       @OA\Property(property="service_id", type="numeric", example="1/2"),
     *       @OA\Property(property="discription", type="string", example="Lorem Ipsum is simply dummy text"),
     *       @OA\Property(property="gender", type="string", format="email", example="Male/Female/Both"),
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

    public function createServiceCategory(Request $request)
    {   
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $idRules          = ['service_id' => 'required|numeric'];
        $nameRules        = ['name' => 'required'];
        $discriptionRules = ['discription' => 'required'];
        $genderRules      = ['gender' => 'required'];

        //Service id validation...
        $validId = Validator::make($request->all(), $idRules);
        if ($validId->fails())
            return ResponseUtility::respondWithError(4018, null);

        //name validation...
        $validName = Validator::make($request->all(), $nameRules);
        if ($validName->fails())
            return ResponseUtility::respondWithError(4014, null);

        //Discription validation...
        $validDiscription = Validator::make($request->all(), $discriptionRules);
        if ($validDiscription->fails())
            return ResponseUtility::respondWithError(4015, null);

        //Gender validation...
        $validGender = Validator::make($request->all(), $genderRules);
        if ($validGender->fails())
            return ResponseUtility::respondWithError(4016, null);

        //Dyanamic Name Validation..
        $isName = $partnerService->isCatNameExists($payload['name'], $payload['account_id']);
        if($isName)
            return ResponseUtility::respondWithError(4019, $payload['name']);

        $lastInsertedID = $partnerService->createServiceCategory($payload);
        return ResponseUtility::respondWithSuccess(4511, $lastInsertedID);
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/category/list_all_category",
     * summary="List all categories.",
     *   description="List all categories codes.<br/>
      Success Code:<br/>
            4512: Categories list fetched successfully.<br/>
       Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
       ",
     * tags={"Partner Management"},
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
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function listAllCategories(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        //Actual take care of DB
        $categoriesList = $partnerService->listAllCategories($payload);
        return ResponseUtility::respondWithSuccess(4512, $categoriesList);
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/category/get_details",
     * summary="Category details.",
     *   description="Category details codes.<br/>
      Success Code:<br/>
            4513: Category details fetched successfully.<br/>
       Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
            4020: Please enter a valid category id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="cat_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function getCategoryDetails(Request $request) 
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $idRules = ['cat_id' => 'required|numeric'];

        //Category id validation...
        $validId = Validator::make($request->all(), $idRules);
        if ($validId->fails())
            return ResponseUtility::respondWithError(4020, null);

        //Dynamic category id validation..
        $count = $partnerService->countCatID($payload['account_id'], $payload['cat_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4020, $payload['cat_id']);

        //Actual take care of DB
        $categoryDetails = $partnerService->getCategoryDetails($payload);
        return ResponseUtility::respondWithSuccess(4513, $categoryDetails);
    }

    /**
     * @OA\Post(
     * path="/api/partner/service/category/update",
     * summary="Update service category.",
      *   description="Update service category codes.<br/>
       Success Code:<br/>
       4514: Category updated successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4014: Please enter a valid name.<br/>
       4015: Please enter a valid discription.<br/>
       4016: Please enter a valid gender.<br/>
       4018: Please enter a valid service id.<br/>
       4019: This Category name already exists.<br/>
       4020: Please enter a valid category id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"service_id","name","discription", "gender"},
     *       @OA\Property(property="cat_id", type="numeric", example="1/2"),
     *       @OA\Property(property="name", type="string", example="Skin_care/Hair_care"),
     *       @OA\Property(property="service_id", type="numeric", example="1/2"),
     *       @OA\Property(property="discription", type="string", example="Lorem Ipsum is simply dummy text"),
     *       @OA\Property(property="status", type="numeric", example="0/1"),
     *       @OA\Property(property="gender", type="string", format="email", example="Male/Female/Both"),
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

    public function updateCategory(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $catIDRules       = ['cat_id' => 'required|numeric'];
        $serviceIDRules   = ['service_id' => 'required|numeric'];
        $nameRules        = ['name' => 'required'];
        $discriptionRules = ['discription' => 'required'];
        $genderRules      = ['gender' => 'required'];

        //Category id validation...
        $validCatID = Validator::make($request->all(), $catIDRules);
        if ($validCatID->fails())
            return ResponseUtility::respondWithError(4020, null);

        //Dynamic category id validation..
        $count = $partnerService->countCatID($payload['account_id'], $payload['cat_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4020, $payload['cat_id']);

        //Service id validation...
        $validServiceID = Validator::make($request->all(), $serviceIDRules);
        if ($validServiceID->fails())
            return ResponseUtility::respondWithError(4018, null);

        //name validation...
        $validName = Validator::make($request->all(), $nameRules);
        if ($validName->fails())
            return ResponseUtility::respondWithError(4014, null);

        //Discription validation...
        $validDiscription = Validator::make($request->all(), $discriptionRules);
        if ($validDiscription->fails())
            return ResponseUtility::respondWithError(4015, null);

        //Gender validation...
        $validGender = Validator::make($request->all(), $genderRules);
        if ($validGender->fails())
            return ResponseUtility::respondWithError(4016, null);
    
        //Dyanamic Name Validation..
        $isName = $partnerService->countCatName($payload['name'], $payload['account_id'], $payload['cat_id']);
        if($isName)
            return ResponseUtility::respondWithError(4019, $payload['name']);
        
        $update = $partnerService->updateCategory($payload);
        return ResponseUtility::respondWithSuccess(4514, $update);
    }

     /**
     * @OA\Post(
     * path="/api/partner/service/category/status_update",
     * summary="Status update service.",
      *   description="Status service codes.<br/>
       Success Code:<br/>
       4524: Category status update successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4020: Please enter a valid category id.<br/>
       4034: Please enter a status.</br>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"category_id","status"},
     *       @OA\Property(property="category_id", type="string", example="1/2"),
     *       @OA\Property(property="status", type="string", example="0/1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, Please try again")
     *        )
     *     )
     * )
     */

    public function categoryStatusUpdate(Request $request)
    {
        $payload = $request->all();

        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $categoryID = ['cat_id' => 'required|numeric'];
        $status     = ['status'     =>  'required|numeric'];

        //serviceID validation
        $categoryIDValidation = Validator::make($request->all(),$categoryID);
        if ($categoryIDValidation->fails()) {
            return ResponseUtility::respondWithError(4020, null);
        }

        //status Validation..
        $statusValidation = Validator::make($request->all(), $status);
        if ($statusValidation->fails()) {
            return ResponseUtility::respondWithError(4034 ,null);
        }

        $count = $partnerService->countCatID($payload['account_id'], $payload['cat_id']);
        if ($count == 0){
            return ResponseUtility::respondWithError(4020, $payload['cat_id']);
        }

        $statusUpdate = $partnerService->categoryStatusUpdate($payload);
        return ResponseUtility::respondWithSuccess(4524,$statusUpdate);
    }

    /**
     * @OA\Delete(
     * path="/api/partner/service/category/delete",
     * summary="Delete Category",
     *   description="Delete Category.<br/>
      Success Code:<br/>
            4520: Category deleted successfully.<br/>
      Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            4018: Please enter a Category id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="category_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, Please try again")
     *        )
     *     )
     * )
     */

     public function deleteCategory(Request $request)
     {
        $payload = $request->all();

        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;
        
        $categoryID = ['cat_id' => 'required|numeric'];

        $categoryIDValidation = Validator::make($request->all(), $categoryID);
        if ($categoryIDValidation->fails()) {
           return ResponseUtility::respondWithError(4020, null);
        }

        $count = $partnerService->countCatID($payload['account_id'], $payload['cat_id']);
        if ($count == 0){
            return ResponseUtility::respondWithError(4020, $payload['cat_id']);
        }

        $delete = $partnerService->deleteCategory($payload);
        return ResponseUtility::respondWithSuccess(4521 , $delete);
     }

    /**
     * @OA\Post(
     * path="/api/partner/service/category/add_subtype",
     * summary="Add service subtype.",
      *   description="Add service subtype codes.<br/>
       Success Code:<br/>
       4515: Service subtype created successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4014: Please enter a valid name.<br/>
       4015: Please enter a valid discription.<br/>
       4016: Please enter a valid gender.<br/>
       4018: Please enter a valid service id.<br/>
       4020: Please enter a valid category id.<br/>
       4021: Please enter a valid price.<br/>
       4022: Please enter a valid time.<br/>
       4023: This Service subtype name already exists.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"service_id","cat_id","name","discription", "gender","price","time"},
     *       @OA\Property(property="name", type="string", example="Smooth_clean/simple cut hair"),
     *       @OA\Property(property="service_id", type="numeric", example="1/2"),
     *       @OA\Property(property="cat_id", type="numeric", example="1/2"),
     *       @OA\Property(property="discription", type="string", example="Lorem Ipsum is simply dummy text"),
     *       @OA\Property(property="gender", type="string", format="email", example="Male/Female/Both"),
     *       @OA\Property(property="price", type="numeric", example="5/10"),
     *       @OA\Property(property="time", type="string", example="11:48am"),
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

    public function createSubType(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $serviceIDRules   = ['service_id' => 'required|numeric'];
        $catIDRules       = ['cat_id' => 'required|numeric'];
        $nameRules        = ['name' => 'required'];
        $discriptionRules = ['discription' => 'required'];
        $genderRules      = ['gender' => 'required'];
        $priceRules       = ['price' => 'required|numeric'];
        $timeRules        = ['time' => 'required'];

        //Service id validation...
        $validServiceId = Validator::make($request->all(), $serviceIDRules);
        if ($validServiceId->fails())
            return ResponseUtility::respondWithError(4018, null);

        //Category id validation...
        $validCatID = Validator::make($request->all(), $catIDRules);
        if ($validCatID->fails())
            return ResponseUtility::respondWithError(4020, null);

        //name validation...
        $validName = Validator::make($request->all(), $nameRules);
        if ($validName->fails())
            return ResponseUtility::respondWithError(4014, null);

        //Discription validation...
        $validDiscription = Validator::make($request->all(), $discriptionRules);
        if ($validDiscription->fails())
            return ResponseUtility::respondWithError(4015, null);

        //Gender validation...
        $validGender = Validator::make($request->all(), $genderRules);
        if ($validGender->fails())
            return ResponseUtility::respondWithError(4016, null);

        //Price validation...
        $validPrice = Validator::make($request->all(), $priceRules);
        if ($validPrice->fails())
            return ResponseUtility::respondWithError(4021, null);

        //Time validation...
        $validTime = Validator::make($request->all(), $timeRules);
        if ($validTime->fails())
            return ResponseUtility::respondWithError(4022, null);

        //Dyanamic Name Validation..
        $isName = $partnerService->isSubTypeNameExists($payload['name'], $payload['account_id']);
        if($isName)
            return ResponseUtility::respondWithError(4023, $payload['name']);
    
        $lastInsertedID = $partnerService->createSubType($payload);
        return ResponseUtility::respondWithSuccess(4515, $lastInsertedID);
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/category/list_all_subtype",
     * summary="List all service subtypes.",
     *   description="List all service subtypes codes.<br/>
      Success Code:<br/>
            4516: Service subtypes list fetched successfully.<br/>
       Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
       ",
     * tags={"Partner Management"},
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
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function listAllServiceSubtypes(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        //Actual take care of DB
        $categoriesList = $partnerService->listAllServiceSubtypes($payload);
        return ResponseUtility::respondWithSuccess(4516, $categoriesList);
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/category/get_subtype_details",
     * summary="Service subtype details.",
     *   description="Service subtype details codes.<br/>
      Success Code:<br/>
            4517: Service subtype details fetched successfully.<br/>
       Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
            4024: Please enter a valid subtype id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="subtype_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function getSubTypeDetails(Request $request) 
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $subTypeRules = ['subtype_id' => 'required|numeric'];

        //Subtype id validation...
        $validSubType = Validator::make($request->all(), $subTypeRules);
        if ($validSubType->fails())
            return ResponseUtility::respondWithError(4024, null);

        //Dynamic subtype id validation..
        $count = $partnerService->countSubTypeID($payload['account_id'], $payload['subtype_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4024, $payload['subtype_id']);
    
        //Actual take care of DB
        $subTypeDetails = $partnerService->getSubTypeDetails($payload);
        return ResponseUtility::respondWithSuccess(4517, $subTypeDetails);
    }

    /**
     * @OA\Post(
     * path="/api/partner/service/category/update_subtype",
     * summary="Update service subtype.",
      *   description="Update service subtype codes.<br/>
       Success Code:<br/>
       4518: Service subtype updated successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4014: Please enter a valid name.<br/>
       4015: Please enter a valid discription.<br/>
       4016: Please enter a valid gender.<br/>
       4018: Please enter a valid service id.<br/>
       4020: Please enter a valid category id.<br/>
       4021: Please enter a valid price.<br/>
       4022: Please enter a valid time.<br/>
       4023: This Service subtype name already exists.<br/>
       4024: Please enter a valid subtype id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"subtype_id","service_id","cat_id","name","discription", "gender","price","time"},
     *       @OA\Property(property="subtype_id", type="numeric", example="1/2"),
     *       @OA\Property(property="service_id", type="numeric", example="1/2"),
     *       @OA\Property(property="cat_id", type="numeric", example="1/2"),
     *       @OA\Property(property="name", type="string", example="Smooth_clean/simple cut hair"),
     *       @OA\Property(property="discription", type="string", example="Lorem Ipsum is simply dummy text"),
     *       @OA\Property(property="gender", type="string", format="email", example="Male/Female/Both"),
     *       @OA\Property(property="status", type="numeric", example="0/1"),
     *       @OA\Property(property="price", type="numeric", example="5/10"),
     *       @OA\Property(property="time", type="string", example="11:48am"),
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

    public function updateSubType(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $serviceIDRules   = ['service_id' => 'required|numeric'];
        $catIDRules       = ['cat_id' => 'required|numeric'];
        $nameRules        = ['name' => 'required'];
        $discriptionRules = ['discription' => 'required'];
        $genderRules      = ['gender' => 'required'];
        $priceRules       = ['price' => 'required|numeric'];
        $timeRules        = ['time' => 'required'];
        $subTypeRules     = ['subtype_id' => 'required|numeric'];

        //Subtype id validation...
        $validSubType = Validator::make($request->all(), $subTypeRules);
        if ($validSubType->fails())
            return ResponseUtility::respondWithError(4024, null);

        //Dynamic subtype id validation..
        $count = $partnerService->countSubTypeID($payload['account_id'], $payload['subtype_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4024, $payload['subtype_id']);

        //Service id validation...
        $validServiceId = Validator::make($request->all(), $serviceIDRules);
        if ($validServiceId->fails())
            return ResponseUtility::respondWithError(4018, null);

        //Category id validation...
        $validCatID = Validator::make($request->all(), $catIDRules);
        if ($validCatID->fails())
            return ResponseUtility::respondWithError(4020, null);

        //name validation...
        $validName = Validator::make($request->all(), $nameRules);
        if ($validName->fails())
            return ResponseUtility::respondWithError(4014, null);

        //Discription validation...
        $validDiscription = Validator::make($request->all(), $discriptionRules);
        if ($validDiscription->fails())
            return ResponseUtility::respondWithError(4015, null);

        //Gender validation...
        $validGender = Validator::make($request->all(), $genderRules);
        if ($validGender->fails())
            return ResponseUtility::respondWithError(4016, null);

        //Price validation...
        $validPrice = Validator::make($request->all(), $priceRules);
        if ($validPrice->fails())
            return ResponseUtility::respondWithError(4021, null);

        //Time validation...
        $validTime = Validator::make($request->all(), $timeRules);
        if ($validTime->fails())
            return ResponseUtility::respondWithError(4022, null);

        //Dyanamic Name Validation..
        $isName = $partnerService->countSubTypeName($payload['name'], $payload['account_id'], $payload['subtype_id']);
        if($isName)
            return ResponseUtility::respondWithError(4023, $payload['name']);
    
        $update = $partnerService->updateSubType($payload);
        return ResponseUtility::respondWithSuccess(4518, $update);
    } 

    /**
     * @OA\Post(
     * path="/api/partner/service/category/subtype/status_update",
     * summary="Status update subtype.",
      *   description="Status subtype codes.<br/>
       Success Code:<br/>
       4525: Subtype status update successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4024: Please enter a valid subtype id.<br/>
       4034: Please enter a status.</br>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"subtype_id","status"},
     *       @OA\Property(property="subtype_id", type="string", example="1/2"),
     *       @OA\Property(property="status", type="string", example="0/1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, Please try again")
     *        )
     *     )
     * )
     */

    public function subtypeStatusUpdate(Request $request)
    {
        $payload = $request->all();

        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $subtypeID = ['subtype_id' => 'required|numeric'];
        $status    = ['status'     =>  'required'];

        //subtypeID validation
        $serviceIDValidation = Validator::make($request->all(),$subtypeID);
        if ($serviceIDValidation->fails()) {
            return ResponseUtility::respondWithError(4024, null);
        }

        //status Validation..
        $statusValidation = Validator::make($request->all(), $status);
        if ($statusValidation->fails()) {
            return ResponseUtility::respondWithError(4034 ,null);
        }

        //Dynamic subtype id validation..
        $count = $partnerService->countSubTypeID($payload['account_id'], $payload['subtype_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4024, $payload['subtype_id']);


        $statusUpdate = $partnerService->subtypeStatusUpdate($payload);
        return ResponseUtility::respondWithSuccess(4525,$statusUpdate);

    }
    
    /**
     * @OA\Delete(
     * path="/api/partner/service/category/sub_type/delete",
     * summary="Delete Sub-Type",
     *   description="Delete Sub-Type.<br/>
      Success Code:<br/>
            4520: Subtype deleted successfully.<br/>
      Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            4018: Please enter a Category id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sub_type_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, Please try again")
     *        )
     *     )
     * )
     */

     public function deleteSubType(Request $request)
     {
        $payload = $request->all();

        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;
        
        $subTypeID = ['subtype_id' => 'required|numeric'];

        $subTypeIDValidation = Validator::make($request->all(), $subTypeID );
        if ($subTypeIDValidation->fails()) {
           return ResponseUtility::respondWithError(4024, null);
        }

        //Dynamic subtype id validation..
        $count = $partnerService->countSubTypeID($payload['account_id'], $payload['subtype_id']);
        if ($count == 0)
            return ResponseUtility::respondWithError(4024, $payload['subtype_id']);


        $delete = $partnerService->deleteSubType($payload);
        return ResponseUtility::respondWithSuccess(4522 , $delete);
     }

    /**
     * @OA\Get(
     * path="/api/partner/service/category_list",
     * summary="Category list from service.",
     *   description="Category list from service codes.<br/>
      Success Code:<br/>
            4512: Categories list fetched successfully.<br/>
       Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
            4018: Please enter a valid service id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="service_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function getCategoriesList(Request $request) 
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $serviceIDRules   = ['service_id' => 'required|numeric'];

        //Service id validation...
        $validServiceId = Validator::make($request->all(), $serviceIDRules);
        if ($validServiceId->fails())
            return ResponseUtility::respondWithError(4018, null);
    
        //Actual take care of DB
        $categoriesList = $partnerService->getCategoriesList($payload);
        return ResponseUtility::respondWithSuccess(4512, $categoriesList);
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/category/subtype_list",
     * summary="Subtype list from category.",
     *   description="Subtype list from category codes.<br/>
      Success Code:<br/>
            4516: Service subtypes list fetched successfully.<br/>
       Error Code:<br/>
            40001: Invalid session-token or its expired.<br/>
            40002: Custom Header values are required.<br/>
            4020: Please enter a valid category id.<br/>
       ",
     * tags={"Partner Management"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="cat_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function getSubTypeList(Request $request) 
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $partnerService = new PartnerServicesService();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $catIDRules = ['cat_id' => 'required|numeric'];

        //Category id validation...
        $validCatID = Validator::make($request->all(), $catIDRules);
        if ($validCatID->fails())
            return ResponseUtility::respondWithError(4020, null);
    
        //Actual take care of DB
        $categoriesList = $partnerService->getSubTypeList($payload);
        return ResponseUtility::respondWithSuccess(4516, $categoriesList);
    }

}
