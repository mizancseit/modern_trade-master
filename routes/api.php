<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('apps/api/attendance','Sales\MasterAppsController@ssg_apps_api_attendance');
Route::post('apps/order_confirm','Sales\MasterAppsController@ssg_apps_api_order_confirm');
Route::post('apps/discount','Sales\MasterAppsController@ssg_apps_api_discount');
Route::post('apps/order-manage-list','Sales\MasterAppsController@ssg_apps_api_order_manage_list');


//Real-time Update API

Route::post('apps/point-list','Sales\MasterAppsController@ssg_apps_api_point_list');
Route::post('apps/route-list','Sales\MasterAppsController@ssg_apps_api_route_list');
Route::post('apps/retailer-list','Sales\MasterAppsController@ssg_apps_api_retailer_list');
Route::post('apps/distributor-list','Sales\MasterAppsController@ssg_apps_api_distributor_list');
Route::post('apps/fo-list','Sales\MasterAppsController@ssg_apps_api_fo_list');
Route::post('apps/category-list','Sales\MasterAppsController@ssg_apps_api_category_list');
Route::post('apps/product-list','Sales\MasterAppsController@ssg_apps_api_product_list');


// new retailer
Route::post('apps/new-retailer-submit','Sales\MasterAppsController@ssg_new_retailer_submit');

// retailer active/inactive
Route::post('apps/retailer-active-inactive-submit','Sales\MasterAppsController@ssg_retailer_active_inactive_submit');
