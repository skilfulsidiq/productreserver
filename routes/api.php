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

Route::group(['namespace'=>'App\Http\Controllers\Api'],function(){

        Route::post('login','AuthController@login');
        Route::post('register','AuthController@register');
        Route::post('forgot-password','AuthController@forgot_password');
        Route::post('change-password-code','AuthController@changePasswordWithCode');

        Route::group(['middleware'=>['auth:sanctum']],function(){
             //verify email
            Route::post('verify-email','AuthController@login');
             //product list
            Route::get('all-products','ProductController@productList');
             //reserve a product
            Route::post('reserve-products','ReserveProductController@reserveForAProduct');
             //user reserved products lists
            Route::get('all-reserved-products','ReserveProductController@userReservedProducts');

             //admin
            Route::group(['prefix'=>'admin'],function(){

                //product list
                Route::get('all-products','ProductController@adminProductList');
                
                //addorupdate proudcy
                Route::get('update-product/{slug?}','ProductController@addOrUpdateProduct');

                //delete product
                Route::delete('delete-product/{slug}','ProductController@deleteProduct');

                //view all reserved products
                Route::get('all-reserved-products','ReserveProductController@allReservedProducts');

                //user list
                Route::get('all-users','UserController@allUsers');
                //update user
                Route::get('update-user/{slug?}','UserController@updateUser');
                //delete user
                Route::delete('delete-user/{slug}','UserController@deleteUser');

            });
        });

});
