<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::Post('user/add_user', 'App\Http\Controllers\UserController@CreateUsers');
Route::post('users/login', 'App\Http\Controllers\UserController@loginAccount');
Route::get('users/validate_session_token', 'App\Http\Controllers\UserController@validateSessionToken');
Route::post('users/forgot_password', 'App\Http\Controllers\UserController@forgotPassword');
Route::post('users/validate_forgot_link', 'App\Http\Controllers\UserController@validateForgotLink');
Route::post('users/reset_password', 'App\Http\Controllers\UserController@resetPassword');


//Shop Owner Service management
Route::post('partner/service/add', 'App\Http\Controllers\PartnerServiceController@createService')->middleware('check.headers.auth'); 
Route::get('partner/service/list_all_service', 'App\Http\Controllers\PartnerServiceController@listAllService')->middleware('check.headers.auth');
Route::get('partner/service/get_details', '   App\Http\Controllers\PartnerServiceController@getServiceDetails')->middleware('check.headers.auth');
Route::post('partner/service/update', 'App\Http\Controllers\PartnerServiceController@updateService')->middleware('check.headers.auth');
Route::post('partner/service/category/add', 'App\Http\Controllers\PartnerServiceController@createServiceCategory')->middleware('check.headers.auth');
Route::get('partner/service/category/list_all_category', 'App\Http\Controllers\PartnerServiceController@listAllCategories')->middleware('check.headers.auth'); 
Route::get('partner/service/category/get_details', 'App\Http\Controllers\PartnerServiceController@getCategoryDetails')->middleware('check.headers.auth');
Route::post('partner/service/category/update', 'App\Http\Controllers\PartnerServiceController@updateCategory')->middleware('check.headers.auth');
Route::post('partner/service/category/add_subtype', 'App\Http\Controllers\PartnerServiceController@createSubType')->middleware('check.headers.auth');
Route::get('partner/service/category/list_all_subtype', 'App\Http\Controllers\PartnerServiceController@listAllServiceSubtypes')->middleware('check.headers.auth');
Route::get('partner/service/category/get_subtype_details', 'App\Http\Controllers\PartnerServiceController@getSubTypeDetails')->middleware('check.headers.auth');
Route::post('partner/service/category/update_subtype', 'App\Http\Controllers\PartnerServiceController@updateSubType')->middleware('check.headers.auth');
Route::get('partner/service/category_list', 'App\Http\Controllers\PartnerServiceController@getCategoriesList')->middleware('check.headers.auth');
Route::get('partner/service/category/subtype_list', 'App\Http\Controllers\PartnerServiceController@getSubTypeList')->middleware('check.headers.auth');


//Shop Owner Services List..    
Route::get('partner/service/list_all_public', 'App\Http\Controllers\ListController@listAllServicePublic');
Route::get('partner/service/category/list_all_public', 'App\Http\Controllers\ListController@listAllCategoriesPublic');
Route::get('partner/service/category/list_all_subtype_public', 'App\Http\Controllers\ListController@listAllServiceSubtypesPublic');

// private 
Route::get('partner/service/serch_filled_cat_list', 'App\Http\Controllers\ListController@autoSuggestionServices')->middleware('check.headers.auth');
Route::get('partner/service/serch_filled_cat', 'App\Http\Controllers\ListController@autoSuggestionCategories')->middleware('check.headers.auth');
Route::get('partner/service/serch_filled', 'App\Http\Controllers\ListController@autoSuggestionSubtypes')->middleware('check.headers.auth');

// public..  
Route::get('partner/service/serch_filled_cat_list_new', 'App\Http\Controllers\ListController@autoSuggestionServicesPublic');
Route::get('partner/service/category/list_all_public_new', 'App\Http\Controllers\ListController@autoSuggestionCategoriesPublic');
Route::get('partner/service/serch_filled_new', 'App\Http\Controllers\ListController@autoSuggestionSubtypesPublic');
//Route::get('partner/service/sub_list', 'App\Http\Controllers\ListController@serviceList');// using join..

// partner service delete api 
Route::delete('partner/service/delete', 'App\Http\Controllers\PartnerServiceController@deleteService')->middleware('check.headers.auth');
Route::delete('partner/service/category/delete', 'App\Http\Controllers\PartnerServiceController@deleteCategory')->middleware('check.headers.auth');
Route::delete('partner/service/category/sub_type/delete', 'App\Http\Controllers\PartnerServiceController@deleteSubType')->middleware('check.headers.auth');

// partner services for status update services..
Route::post('partner/service/status_update', 'App\Http\Controllers\PartnerServiceController@serviceStatusUpdate')->middleware('check.headers.auth');
Route::post('partner/service/category/status_update', 'App\Http\Controllers\PartnerServiceController@categoryStatusUpdate')->middleware('check.headers.auth');
Route::post('partner/service/category/subtype/status_update', 'App\Http\Controllers\PartnerServiceController@subtypeStatusUpdate')->middleware('check.headers.auth');

// List timeing api..

Route::post('list/time/all_time', 'App\Http\Controllers\ListController@listOfTime');
Route::post('list/time/book','App\Http\Controllers\ListController@bookAppointment')->middleware('check.headers.auth');
