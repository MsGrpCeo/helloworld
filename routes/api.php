<?php

use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserDevController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'App\Http\Controllers\Api\V1'], function() {

  Route::post('/user/register_user', [UserController::class, 'registerUser']);
  Route::post('/user/signin', [UserController::class, 'loginUser']);
  Route::post('/user/login', [UserController::class, 'loginUser']);
  // Route::post('/user/login', function(Request $request) {
  //   dd($request);
  // });
  Route::post('/forgot-password',[UserController::class, 'forgotPassword'])->middleware('guest');
  // Route::post('/rc/apple-server-notifications', [UserController::class, 'processAppleServerNotification']);
  
  Route::get('/user/get_equipment_sample_subimages', [UserController::class, 'getEquipmentSamleSubImages']);
  Route::get('/user/get_equipment_sample_images', [UserController::class, 'getEquipmentSampleImages']);
  Route::get('/user/get_equipment_list', [UserController::class, 'getEquipmentList']);
  Route::get('/user/get_contacts', [UserController::class, 'getContacts']);
  Route::get('/user/check_exist_email', [UserController::class, 'checkExistEmail']);
  Route::get('/user/get_time', [UserController::class, 'getTime']);
  Route::post('/user/contactus', [UserController::class, 'contactUs']);
  Route::post('/user/apple_signin', [UserController::class, 'appleSignin']);
  Route::post('/user/google_signin', [UserController::class, 'googleSignin']);
  Route::post('/user/fb_signin', [UserController::class, 'fbSignin']);
  Route::post('/user/send_verify_email', [UserController::class, 'sendVerifyEmail']);
  Route::get('/send_link', [UserController::class, 'frmSendLink'])->name('frm_send_link');
  Route::post('/send_link', [UserController::class, 'sendLink'])->name('send_link');
  Route::get('/reset_pass/{token}', [UserController::class, 'frmResetPass'])->name('reset_pass');
  Route::post('/reset_pass', [UserController::class, 'resetPass'])->name('post_reset_pass');
});


Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'App\Http\Controllers\Api\V1', 'middleware' => 'jwt.verify'], function() {
  Route::post('/edit_push_setting', [UserController::class, 'editPushSetting']);
  Route::get('/user/current', [UserController::class, 'currentUser']);
  Route::post('/user/edit_profile', [UserController::class, 'editProfile']);
  Route::post('/user/add_profile_photo', [UserController::class, 'addProfilePhoto']);
  Route::post('/user/delete_account', [UserController::class, 'deleteAccount']);
  Route::post('/user/change_password', [UserController::class, 'changePassword']);
  Route::post('/user/upload_equip_image', [UserController::class, 'uploadEquipImage']);
  Route::get('/user/get_forms', [UserController::class, 'getForms']);
  Route::post('/user/submit_form', [UserController::class, 'submitForm']);
});



Route::group(['prefix' => 'dev', 'as' => 'api_dev.', 'namespace' => 'App\Http\Controllers\Api\V1'], function() {
  Route::post('/user/register_user', [UserDevController::class, 'registerUser']);
  Route::post('/user/signin', [UserDevController::class, 'loginUser']);
  Route::post('/user/login', [UserDevController::class, 'loginUser']);
  Route::post('/forgot-password',[UserDevController::class, 'forgotPassword'])->middleware('guest');
  // Route::post('/rc/apple-server-notifications', [UserController::class, 'processAppleServerNotification']);
  
  Route::get('/user/get_equipment_sample_subimages', [UserDevController::class, 'getEquipmentSamleSubImages']);
  Route::get('/user/get_equipment_sample_images', [UserDevController::class, 'getEquipmentSampleImages']);
  Route::get('/user/get_equipment_list', [UserDevController::class, 'getEquipmentList']);
  Route::get('/user/get_contacts', [UserDevController::class, 'getContacts']);
  Route::get('/user/check_exist_email', [UserDevController::class, 'checkExistEmail']);
  Route::get('/user/get_time', [UserDevController::class, 'getTime']);
  Route::post('/user/contactus', [UserDevController::class, 'contactUs']);
  Route::post('/user/apple_signin', [UserDevController::class, 'appleSignin']);
  Route::post('/user/google_signin', [UserDevController::class, 'googleSignin']);
  Route::post('/user/fb_signin', [UserDevController::class, 'fbSignin']);
  Route::post('/user/send_verify_email', [UserDevController::class, 'sendVerifyEmail']);
  Route::get('/send_link', [UserDevController::class, 'frmSendLink'])->name('frm_send_link');
  Route::post('/send_link', [UserDevController::class, 'sendLink'])->name('send_link');
  Route::get('/reset_pass/{token}', [UserDevController::class, 'frmResetPass'])->name('reset_pass');
  Route::post('/reset_pass', [UserDevController::class, 'resetPass'])->name('post_reset_pass');
});


Route::group(['prefix' => 'dev', 'as' => 'api_dev.', 'namespace' => 'App\Http\Controllers\Api\V1', 'middleware' => ['dev.jwt.verify', 'dev.db_connection']], function() {
  Route::post('/edit_push_setting', [UserDevController::class, 'editPushSetting']);
  Route::get('/user/current', [UserDevController::class, 'currentUser']);
  Route::post('/user/edit_profile', [UserDevController::class, 'editProfile']);
  Route::post('/user/add_profile_photo', [UserDevController::class, 'addProfilePhoto']);
  Route::post('/user/delete_account', [UserDevController::class, 'deleteAccount']);
  Route::post('/user/send_verify_email', [UserDevController::class, 'sendVerifyEmail']);
  Route::post('/user/change_password', [UserDevController::class, 'changePassword']);
  Route::post('/user/upload_equip_image', [UserDevController::class, 'uploadEquipImage']);
  Route::get('/user/get_forms', [UserDevController::class, 'getForms']);
  Route::post('/user/submit_form', [UserDevController::class, 'submitForm']);
});