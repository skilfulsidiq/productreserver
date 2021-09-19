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
        Route::post('forgot-password','AuthController@forgotPassword');
        Route::post('change-password-code','AuthController@changePasswordWithCode');

        Route::group(['middleware'=>['auth:sanctum']],function(){
             //verify email
            Route::post('verify-email','AuthController@verifyEmail');
            Route::post('logout','AuthController@logout');
            //resend code
            Route::get('resend-code','AuthController@resendVerificationCode');
             //product list
            Route::get('all-products','ProductController@productinatedPagList');
             //reserve a product
            Route::get('reserve-products/{id}','ReserveProductController@reserveForAProduct');
            Route::get('unreserve-products/{id}','ReserveProductController@unReserveForAProduct');
             //user reserved products lists
            Route::get('all-reserved-products','ReserveProductController@userReservedProducts');

             //admin
            Route::group(['prefix'=>'admin','middleware'=>'isAdmin'],function(){
                Route::get('dashboard','ProductController@adminDashboard');


                //product list
                
                
                Route::get('all-products','ProductController@productList');

                //addorupdate proudcy
                Route::post('update-product/{slug?}','ProductController@addOrUpdateProduct');

                //delete product
                Route::delete('delete-product/{slug}','ProductController@deleteProduct');

                //view all reserved products
                Route::get('all-reserved-products','ReserveProductController@allReservedProducts');

                //roles list
                 Route::get('all-roles','UserController@allRoles');
                //user list
                Route::get('all-users','UserController@allUsers');
                //update user
                Route::post('update-user/{slug?}','UserController@updateUser');
                //delete user
                Route::delete('delete-user/{slug}','UserController@deleteUser');

            });
        });

});
