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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::group(
    ['middleware' => ['api'], 'module' => 'eshop', 'namespace' => 'App\Modules\eshop\Controllers'], function () {

Route::get('api/eshop-add-to-cart-orders','MasterAppsController@eshop_api_add_to_cart_orders');
//Route::get('api/eshop-add-to-cart-products','MasterAppsController@eshop_api_add_to_cart_products');
Route::post('api/eshop-add-to-cart-products','MasterAppsController@eshop_api_add_to_cart_products');

Route::post('apps/order_confirm','Sales\MasterAppsController@ssg_apps_api_order_confirm');

 }
);