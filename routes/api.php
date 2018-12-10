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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix' => 'v1', 'namespace' => 'API'], function(){
  //auth user
  Route::post('login', 'AuthController@login');
  Route::post('register', 'AuthController@register');

  Route::group(['middleware' => 'jwt.auth'], function () {
      Route::post('logout', 'AuthController@logout');
      Route::get('account/profile', 'UserController@profile');
      Route::patch('account/changepassword', 'AuthController@changepassword');
      Route::patch('account/profile/update', 'UserController@update');
      Route::post('account/uploadphoto', 'UserController@uploadPhoto');
      //customer
      Route::get('customer', 'CustomerController@index');
      Route::post('customer', 'CustomerController@store');
      Route::patch('customer/{id}', 'CustomerController@update');
      Route::delete('customer/{id}', 'CustomerController@destroy');
      //supplier
      Route::get('supplier', 'SupplierController@index');
      Route::post('supplier', 'SupplierController@store');
      Route::patch('supplier/{id}', 'SupplierController@update');
      Route::delete('supplier/{id}', 'SupplierController@destroy');
      //product
      Route::get('product', 'ProductController@index');
      Route::get('product/{id}', 'ProductController@show');
      Route::post('product/', 'ProductController@store');
      Route::patch('product/{id}', 'ProductController@update');
      Route::delete('product/{id}', 'ProductController@destroy');
      //order
      //select supplier
      Route::get('pembelian', 'OrderController@index');
      Route::post('pembelian', 'OrderController@store');
      //transaksi
      Route::patch('pembelian/{id}', 'OrderController@update');

      Route::get('category', 'CategoriesController@index');
      Route::post('category/create', 'CategoriesController@store');

  });
});
