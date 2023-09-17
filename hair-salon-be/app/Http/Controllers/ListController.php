<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Services\List\ListServices; 
use App\Http\Services\Registration\UserRegistrationService;
use App\Http\Services\Utility\ResponseUtility;
use Validator;

class ListController extends Controller
{   
     /**
     * @OA\Get(
     * path="/api/partner/service/list_all_public",
     * summary="Subtype list from category.",
     * summary="List all Services.",
     *   description="List all Services.<br/>
      Success Code:<br/>
            4512: Categories list fetched successfully.<br/>
            Error Code:<br/>
            4026: Please enter a valid offset.<br/>
            4027: Please enter a valid limit.<br/>
            4028: Please enter a valid name.<br/>
            4029: Please enter a valid sort order.<br/>
       ",
     * tags={"List all"},
     *  @OA\Parameter(
     *      name="offset",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="limit",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sort_by_name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="name"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sort_by",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="asc/desc"
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
     *       @OA\Property(property="message", type="string", example="Sorry,Please try again")
     *        )
     *     )
     * )
     */ 
    public function listAllServicePublic(Request $request)
    {  
        $payload = $request->all();

        $offsetRule     = ['offset'        => 'numeric|required'];
        $nameRule       = ['sort_by_name'  => 'required'];//name 
        $limitRule      = ['limit'         => 'numeric|required'];
        $sortOrderRule  = ['sort_by'       => 'required'];
        $serviceID      = ['service_id'    => 'required|numeric'];

        //offset validation..
        $offstevalidation = Validator::make($request->all(), $offsetRule);
        if($offstevalidation->fails())
        {
            return ResponseUtility::respondWithError(4036, null);
        }
        

        // Service name validation..
        $namevalidation = Validator::make($request->all(), $nameRule);
        if($namevalidation->fails())
        {
            return ResponseUtility::respondWithError(4026, null);
        }

        //limit validation..
        $limitvalidation = Validator::make($request->all(), $limitRule);
        if($limitvalidation->fails())
        {
            return ResponseUtility::respondWithError(4035, null);
        }

        //sort order vaidation..
        $sortOrderValidation = Validator::make($request->all(), $sortOrderRule);
        if($sortOrderValidation->fails())
        {
            return ResponseUtility::respondWithError(4029, null);
        }

        // service id valiadtion..
        $serviceIDValidation = Validator::make($request->all(), $serviceID);
        if ($serviceIDValidation->fails()) {
            
            return ResponseUtility::respondWithError(4032, null);
        }

        $ListService = new ListServices();

        //Actual take care of DB
        $serviceList = $ListService->listAllServicePublic($payload);

        return ResponseUtility::respondWithSuccess(4508, $serviceList);
    }


    // how we can show the data by using two table.. (join)
    // Public function serviceList(Request $request)
    // {   
    //     $payload = $request->all();

    //     $countID = ['service_id' => 'numeric|required'];

    //     $countValidation = Validator::make($request->all(), $countID);
    //     if ($countValidation->fails()) {
            
    //         return ResponseUtility::respondWithError(4032, null);
    //     }
    //     $ListService = new ListServices();

    //     $services = $ListService->serviceList($payload);

    //     return $services;
    // }

     /**
     * @OA\Get(
     * path="/api/partner/service/category/list_all_public",
     * summary="List all Categories.",
     * summary="List all Services.",
     *   description="List all Services.<br/>
      Success Code:<br/>
            4512: Categories list fetched successfully.<br/>
            Error Code:<br/>
            4026: Please enter a valid offset.<br/>
            4027: Please enter a valid limit.<br/>
            4028: Please enter a valid name.<br/>
            4029: Please enter a valid sort order.<br/>
            4030: Please enter a valid service ID.<br/>
       ",
     * tags={"List all"},
     *  @OA\Parameter(
     *      name="offset",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="limit",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sort_by_name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="name"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sort_order",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="asc/desc"
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
     *       @OA\Property(property="message", type="string", example="Sorry,Please try again")
     *        )
     *     )
     * )
     */     
    public function listAllCategoriesPublic(Request $request)
    {
        $payload = $request->all();
        
        $nameRule       = ['sort_by_name'   => 'required'];//name
        $offsetRule     = ['offset'    => 'required|numeric'];
        $limitRule      = ['limit'     => 'required|numeric'];
        $sortOrderRule  = ['sort_order'         => 'required'];
        $serviceIDRule  = ['cat_id' =>      'required|numeric'];
        $countID        = ['service_count' => 'numeric|required'];
        
        //name validation..
        $nameValidation = Validator::make($request->all(), $nameRule);
        if($nameValidation->fails())
        {
                return ResponseUtility::respondWithError(4026, null);
        }

        //limit validation..
        $limitValidation = Validator::make($request->all(), $limitRule);
        if($limitValidation->fails())
        {
            return ResponseUtility::respondWithError(4027, null);
        }

        //offset validation..
        $offsetValidation = Validator::make($request->all(), $offsetRule);
        if($offsetValidation->fails())
        {
            return ResponseUtility::respondWithError(4028, null);
        }
        
        //sort order validation..
        $sortOrderValidation = Validator::make($request->all(), $sortOrderRule);
        if($sortOrderValidation->fails())
        {
            return ResponseUtility::respondWithError(4029, null);
        }

        //service Id validation..
        $serviceIDValidation = Validator::make($request->all(), $serviceIDRule);
        if($serviceIDValidation->fails())
        {
            return ResponseUtility::respondWithError(4030, null);
        }

        $ListService = new ListServices();

        //Actual take care of DB
        $categoriesList = $ListService->listAllCategoriesPublic($payload);
        return ResponseUtility::respondWithSuccess(4512, $categoriesList);
    }
    
    /**
     * @OA\Get(
     * path="/api/partner/service/category/list_all_subtype_public",
     * summary="List all ServiceSubtypes.",
     *   description="List all Services.<br/>
      Success Code:<br/>
            4512: Categories list fetched successfully.<br/>
            Error Code:<br/>
            4026: Please enter a valid offset.<br/>
            4027: Please enter a valid limit.<br/>
            4028: Please enter a valid name.<br/>
            4029: Please enter a valid sort order.</br>
            4030: Please enter a valid service ID.<br/>
            4031: Please enter a valid cat Id. <br/>
       ",
     * tags={"List all"},
     *  @OA\Parameter(
     *      name="cat_id",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="offset",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="limit",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="1/2"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sort_by_name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="name"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="sort_order",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="asc/desc"
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
     *       @OA\Property(property="message", type="string", example="Sorry,Please try again")
     *        )
     *     )
     * )
     */     
    public function listAllServiceSubtypesPublic(Request $request)
    {
        $payload = $request->all();

        $offsetRule     = ['offset' => 'numeric'];
        $limitRule      = ['limit'  => 'numeric'];
        $nameRule       = ['sort_by_name' => 'required'];
        $sortOrderRule  = ['sort_order' => 'in:asc,desc'];
        $serviceIDRule  = ['service_id' => 'required|numeric'];
        $catIDRule      = ['cat_id' => 'required|numeric'];

        // name Validation..
        $nameValidation = Validator::make($request->all(), $nameRule);
        if ($nameValidation->fails())
         {
            return ResponseUtility::respondWithError(4026, null);
        }

        // limit Validation..
        $limitvalidation = Validator::make($request->all(), $limitRule);
        if($limitvalidation->fails()) 
        {
            return ResponseUtility::respondWithError(4027, null);
        }

        //offset validation..
        $offsetValidation = Validator::make($request->all(), $offsetRule);
        if($offsetValidation->fails())
        {
            return ResponseUtility::respondWithError(4028, null);
        }

        //sort validation..
        $sortOrderValidation = Validator::make($request->all(), $sortOrderRule);
        if($sortOrderValidation->fails())
        {
            return ResponseUtility::respondWithError(4029, null);
        }

        //service Id validation..
        $serviceIDValidation = Validator::make($request->all(), $serviceIDRule);
        if($sortOrderValidation->fails())
        {
            return ResponseUtility::respondWithError(4030, null);
        }

        //cat Id validation..
        $catIDValidation = Validator::make($request->all(), $catIDRule);
        if($catIDValidation->fails())
        {
            return ResponseUtility::respondWithError(4031, null);
        }

        $ListService = new ListServices();

        //Actual take care of DB
        $categoriesList = $ListService->listAllServiceSubtypesPublic($payload);
        return ResponseUtility::respondWithSuccess(4516, $categoriesList);
    }

    // private list all services..

    /**
     * @OA\Get(
     * path="/api/partner/service/serch_filled_cat_list",
     * summary="Add service.",
     * description="Add service codes.<br/>
       Success Code:<br/>
       4508: Services list fetched successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4025: Please enter a valid name.<br/>
       ",
     * tags={"List all Private"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="s"
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
    public function autoSuggestionServices(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $ListService = new ListServices();


        $userData = $userRegistrationService->getUserDataByToken($sessionToken);

        $payload['account_id'] = $userData->usr_id;

        $serch = ['name' => 'required'];
        
        //serch validation
        $serchFilled = Validator::make($request->all(), $serch);
        if($serchFilled->fails())
        {
            return ResponseUtility::respondWithError(4025, null);
        }

        $data = $ListService->autoSuggestionServices($payload);
        return ResponseUtility::respondWithSuccess(4508, $data); 
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/serch_filled_cat",
     * summary="Add Catageori.",
     * description="Add catagoreis codes.<br/>
       Success Code:<br/>
       4508: Services list fetched successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4025: Please enter a valid name.<br/>
       ",
     * tags={"List all Private"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="s"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry,Please try again")
     *        )
     *     )
     * )
     */
    public function autoSuggestionCategories(Request $request)
    {

        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $ListService = new ListServices();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $serch = ['name' => 'required'];
       
        //Serch validation
        $serchFilled = Validator::make($request->all(), $serch);
        if ($serchFilled->fails()) 
        {
            return ResponseUtility::respondWithError(4025, null);
        }

         $serchList = $ListService->autoSuggestionCategories($payload);
         return ResponseUtility::respondWithSuccess(4512, $serchList);
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/serch_filled",
     * summary="Add Catageori.",
     * description="Add catagoreis codes.<br/>
       Success Code:<br/>
       4508: Services list fetched successfully.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4025: Please enter a valid name.<br/>
       ",
     * tags={"List all Private"},
     *  @OA\Parameter(
     *      name="session-token",
     *      in="header",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="s"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry,Please try again")
     *        )
     *     )
     * )
     */       
    public function autoSuggestionSubtypes(Request $request)
    {

        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $ListService = new ListServices();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $serch = ['name' => 'required'];
    
        //Serch validation
        $serchFilled = Validator::make($request->all(), $serch);
        if ($serchFilled->fails()) 
        {
            return ResponseUtility::respondWithError(4025, null);
        }

         $ListService = new ListServices();
         $serchList = $ListService->autoSuggestionSubtypes($payload);
         return ResponseUtility::respondWithSuccess(4516, $serchList);
    }



    // Create the all APi are public..

    /**
     * @OA\Get(
     * path="/api/partner/service/serch_filled_cat_list_new",
     * summary="List all Services.",
     *   description="List all Services.<br/>
      Success Code:<br/>
            4516: Service subtypes list fetched successfully.<br/>
            Error Code:<br/>
            4025: Please enter a valid name.<br/>
       ",
     * tags={"List all Public"},
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="s"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry,Please try again")
     *        )
     *     )
     * )
     */     
    public function autoSuggestionServicesPublic(Request $request)
    {
        $payload = $request->all();
         
        $ListService = new ListServices();

        $serch = ['name' => 'required'];
    
        //Serch validation
        $serchFilled = Validator::make($request->all(), $serch);
        if ($serchFilled->fails()) 
        {
            return ResponseUtility::respondWithError(4025, null);
        }

        $serchList = $ListService->autoSuggestionServicesPublic($payload);
        return ResponseUtility::respondWithSuccess(4516, $serchList);

    }

    /**
     * @OA\Get(
     * path="/api/partner/service/category/list_all_public_new",
     * summary="List all Categories.",
     *   description="List all Categories.<br/>
      Success Code:<br/>
            4512: Categories list fetched successfully.<br/>
            Error Code:<br/>
            4025: Please enter a valid name.<br/>
       ",
     * tags={"List all Public"},
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="s"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry,Please try again")
     *        )
     *     )
     * )
     */   
    public function autoSuggestionCategoriesPublic(Request $request)
    {

        $payload = $request->all();

        $ListService = new ListServices();

        $serch = ['name' => 'required'];
       
        //Serch validation
        $serchFilled = Validator::make($request->all(), $serch);
        if ($serchFilled->fails()) 
        {
            return ResponseUtility::respondWithError(4025, null);
        }

         $serchList = $ListService->autoSuggestionCategoriesPublic($payload);
         return ResponseUtility::respondWithSuccess(4512, $serchList);
    }

    /**
     * @OA\Get(
     * path="/api/partner/service/serch_filled_new",
     * summary="List all Subtypes.",
     *   description="List all Subtypes.<br/>
      Success Code:<br/>
            4516: Service subtypes list fetched successfully.<br/>
            Error Code:<br/>
            4025: Please enter a valid name.<br/>
       ",
     * tags={"List all Public"},
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          example="s"
     *      )
     *   ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry,Please try again")
     *        )
     *     )
     * )
     */   
    public function autoSuggestionSubtypesPublic(Request $request)
    {
        $payload = $request->all();
        
        $ListService = new ListServices();

        $serch = ['name' => 'required'];
    
        //Serch validation
        $serchFilled = Validator::make($request->all(), $serch);
        if ($serchFilled->fails()) 
        {
            return ResponseUtility::respondWithError(4025, null);
        }

         $ListService = new ListServices();
         $serchList = $ListService->autoSuggestionSubtypesPublic($payload);
         return ResponseUtility::respondWithSuccess(4516, $serchList);
    }

    /**
     * @OA\Post(
     * path="/api/list/time/book",
     * summary="book appointment.",
      *   description="book apoointment.<br/>
       Success Code:<br/>
       4022: Thank you for booking an appointment.<br/>
       Error Code:<br/>
       40001: Invalid session-token or its expired.<br/>
       40002: Custom Header values are required.<br/>
       4024: Please enter a valid subtype id.<br/>
       4037: Please enter a date.<br/>
       4022: Please enter a valid time.<br/>
       ",
     * tags={"List Controller"},
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
     *       required={"subtype_id","date", "time"},
     *       @OA\Property(property="subtype_id", type="string", example="[1,2,3]"),
     *       @OA\Property(property="date", type="string", example="2023-09-06"),
     *       @OA\Property(property="time", type="string", format="email", example="14:30:00"),
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

    public function bookAppointment(Request $request)
    {
        $payload = $request->all();
        $sessionToken = $request->header('session-token');
        $userRegistrationService = new UserRegistrationService();
        $ListService = new ListServices();

        $userData = $userRegistrationService->getUserDataByToken($sessionToken);
        $payload['account_id'] = $userData->usr_id;

        $subtypeID = ['subtype_id' => 'required'];
        $date      = ['date'       => 'required'];
        $time      = ['time'       => 'required'];

        //subtype id validation..
        $subTypeIDValidation = Validator::make($request->all(), $subtypeID);
        if($subTypeIDValidation->fails()) 
        {
            return ResponseUtility::respondWithError(4024,null);
        }

        //date validation..
        $dateValidation = Validator::make($request->all(), $date);
        if ($dateValidation->fails()) 
        {
            return ResponseUtility::respondWithError(4037,null);
        }

        //time validation.. 
        $timeValidation = Validator::make($request->all(), $time);
        if($timeValidation->fails())
        {
            return ResponseUtility::respondWithError(4022,null);
        }

        $data = $ListService->bookAppointment($payload);

        return ResponseUtility::respondWithSuccess(4526, $data);
    }

 //  public function listOfTime(Request $request)
 //    {
 //    $payload = $request->all();
 //    $sessionToken = $request->header('session-token');
 //    $userRegistrationService = new UserRegistrationService();
 //    $listService = new ListServices();

 //    $userData = $userRegistrationService->getUserDataByToken($sessionToken);

 //    $dateValidation = Validator::make($payload, [
 //        'date' => 'required',
 //    ]);

 //    if ($dateValidation->fails()) {
 //        return ResponseUtility::respondWithError(4037, null);
 //    }

 //    $epochDate = strtotime($payload['date']); // Convert provided date to epoch timestamp

 //    // Check if the epoch date already exists in your database
 //    if ($listService->countDate($epochDate)) {
 //        return ResponseUtility::respondWithError(4038, $payload['date']);
 //    }

 //    $startTime = strtotime('10:10 AM');
 //    $endTime = strtotime('7:55 PM');
 //    $interval = 5 * 60;

 //    $timeList = [];
 //    $statusValue = 0;

 //    while ($startTime <= $endTime) {
 //        $timeSlot = [
 //            'time' => date('h:i A', $startTime),
 //            'status' => $statusValue,
 //        ];

 //        $timeList[] = $timeSlot;
 //        $startTime += $interval;
 //    }

 //    return $timeList;
 // }



public function listOfTimeSS(Request $request)
{
    $payload = $request->all();
    $sessionToken = $request->header('session-token');
    $userRegistrationService = new UserRegistrationService();
    $listService = new ListServices();

    $userData = $userRegistrationService->getUserDataByToken($sessionToken);

    $dateValidation = Validator::make($payload, [
        'date' => 'required',
    ]);

    if ($dateValidation->fails()) {
        return ResponseUtility::respondWithError(4037, null);
    }

    $epochDate = strtotime($payload['date']); // Convert provided date to epoch timestamp

    // Check if the epoch date already exists in your database
    $existingDate = $listService->findExistingDate($epochDate);

    if ($existingDate) {
        $bookedBy = $existingDate->book_by;
        return ResponseUtility::respondWithSuccess('Date already booked by user', ['booked_by' => $bookedBy]);
    }

    // If the date doesn't exist, proceed with generating time slots
    // ...

    $startTime = strtotime('10:10 AM');
    $endTime = strtotime('7:55 PM');
    $interval = 5 * 60;

    $timeList = [];
    $statusValue = 0;

    while ($startTime <= $endTime) {
        $timeSlot = [
            'time' => date('h:i A', $startTime),
            'status' => $statusValue,
        ];

        $timeList[] = $timeSlot;
        $startTime += $interval;
    }

    return $timeList;
}


public function listOfTime(Request $request)
{
    $payload = $request->all();
    $sessionToken = $request->header('session-token');
    $userRegistrationService = new UserRegistrationService();
    $listService = new ListServices();

    $userData = $userRegistrationService->getUserDataByToken($sessionToken);

    $dateValidation = Validator::make($payload, [
        'date' => 'required',
    ]);

    if ($dateValidation->fails()) {
        return ResponseUtility::respondWithError(4037, null);
    }

    $epochDate = strtotime($payload['date']); // Convert provided date to epoch timestamp

    // Check if the epoch date already exists in your database
    $existingDate = $listService->findExistingDate($epochDate);

    if ($existingDate) {
        $bookedBy = $existingDate->book_by;
        
        // If the date exists, check for associated subtypes
        $subtypesForBookedBy = $listService->getSubtypesForBookedBy($bookedBy);

        if (!empty($subtypesForBookedBy)) {
            return ResponseUtility::respondWithSuccess('Date already booked by user with subtypes', [
                'booked_by' => $bookedBy,
                'subtypes' => $subtypesForBookedBy,
            ]);
        } else {
            return ResponseUtility::respondWithSuccess('Date already booked by user, but no subtypes found', [
                'booked_by' => $bookedBy,
                'subtypes' => [],
            ]);
        }
    }

    // If the date doesn't exist, proceed with generating time slots
    // ...

    $startTime = strtotime('10:10 AM');
    $endTime = strtotime('7:55 PM');
    $interval = 5 * 60;

    $timeList = [];
    $statusValue = 0;

    while ($startTime <= $endTime) {
        $timeSlot = [
            'time' => date('h:i A', $startTime),
            'status' => $statusValue,
        ];

        $timeList[] = $timeSlot;
        $startTime += $interval;
    }

    return $timeList;
}



} 


