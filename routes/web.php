<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/cleancache', function () { 
    \Artisan::call("cache:clear");
    \Artisan::call("config:cache");
});

Route::get('/', function () {
    return redirect()->route('login'); 
})->name('index');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::get('/global/checkemail/{email}', 'Admin\RestaurantController@checkGlobalEmail')->name('global.checkemail');
Route::post('register', [RegisterController::class, 'register']);

Route::get('/cron_notification', 'Admin\StoreController@cronScheduleNotification');
Route::get('/update_userlatlong', 'Admin\StoreController@updateUserLatLong');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
  Route::get('/home', [DashboardController::class, 'index'])->name('home.list');

  //Agents management
  Route::get('/agents', [AgentController::class, 'index'])->name('agents.list');
  Route::get('/agents/edit/{userId}', [AgentController::class, 'editUser'])->name('agents.edit');
  Route::get('/agents/getListData', [AgentController::class, 'getListData'])->name('getListData');
  Route::get('/agents/delete/{agent_id}', [AgentController::class, 'deleteUser'])->name('agents.delete');
  Route::get('/agents/checkemail/{email}', [AgentController::class, 'checkUserEmail'])->name('agents.checkemail');
  Route::post('/agents/save', [AgentController::class, 'saveUser'])->name('agents.save');
  Route::get('/agents/download_csv', [AgentController::class, 'downloadCSV'])->name('agents.download_csv');

  // Contact Management
  Route::get('/contact', [ContactController::class, 'index'])->name('contact.list');
  Route::get('/contact/getListData', [ContactController::class, 'getListData'])->name('contact.getListData');
  Route::get('/contact/edit/{id}', [ContactController::class, 'editContact'])->name('contact.edit');
  Route::get('/contact/delete/{id}', [ContactController::class, 'deleteContact'])->name('contact.delete');
  Route::post('/contact/save', [ContactController::class, 'saveData'])->name('contact.save');
  Route::get('/contact/download_csv', [ContactController::class, 'downloadCSV'])->name('contact.download_csv');
  
  //Equipment management
  Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.list');
  Route::get('/equipment/edit/{userId}', [EquipmentController::class, 'editUser'])->name('equipment.edit');
  Route::get('/equipment/getListData', [EquipmentController::class, 'getListData'])->name('getListData');
  Route::post('/equipment/save', [EquipmentController::class, 'saveUser'])->name('equipment.save');
  Route::get('/equipment/delete/{userId}', [EquipmentController::class, 'deleteData'])->name('equipment.delete');
  Route::get('/equipment/move/{prevId}/{curId}', [EquipmentController::class, 'changeOrder'])->name('equipment.move');
  Route::get('/equipment/download_csv', [EquipmentController::class, 'downloadCSV'])->name('equipment.download_csv');
  Route::get('/equipment/sample_images/{equipment_id}', [EquipmentController::class, 'frmSampleImages'])->name('equipment.sample_images');
  Route::get('/equipment/fetch_sample_image_list/{equipment_id}', [EquipmentController::class, 'fetchSampleImageList'])->name('equipment.fetch_sample_image_list');
  Route::get('/equipment/change_image_title/{id}/{title}', [EquipmentController::class, 'changeImageTitle'])->name('equipment.change_image_title');
  Route::get('/equipment/remove_sample_image/{id}', [EquipmentController::class, 'removeSampleImage'])->name('equipment.remove_sample_image');
  Route::post('/equipment/upload_sample_images', [EquipmentController::class, 'postUploadSampleImages'])->name('equipment.upload_sample_images');
  Route::get('/equipment/sort_sample_images', [EquipmentController::class, 'postSortSampleImages'])->name('equipment.sort_sample_images');
  
  //Forms management
  Route::get('/forms', [FormController::class, 'index'])->name('forms.list');
  Route::get('/forms/getListData', [FormController::class, 'getListData'])->name('getListData');
  Route::get('/forms/view/{formId}', [FormController::class, 'viewDetail'])->name('forms.view');
  Route::get('/forms/view_images/{formId}', [FormController::class, 'viewEquipImages'])->name('forms.view_images');
  Route::get('/forms/fetch_images/{formId}', [FormController::class, 'fetchEquipImages'])->name('forms.fetch_images');
  Route::post('/forms/change_equip_image', [FormController::class, 'postChangeEquipImage'])->name('forms.change_equip_image');
  Route::get('/forms/download_excel/{formId}', [FormController::class, 'downloadExcel'])->name('forms.download_excel');
  Route::get('/forms/download_zip/{formId}', [FormController::class, 'downloadZip'])->name('forms.download_zip');
  Route::post('/forms/import_excel', [FormController::class, 'importExcel'])->name('forms.import_excel');
  Route::get('/forms/download_csv/{from}/{to}', [FormController::class, 'downloadCSV'])->name('forms.download_csv');

  //Users management
  Route::get('/users', 'Admin\UserController@index')->name('users.list');
  Route::get('/users/edit/{userId}', 'Admin\UserController@editUser')->name('users.edit');
  Route::get('/users/photo_list/{userId}', 'Admin\UserController@getPhotoList')->name('users.photo_list');
  Route::get('/users/remove_photo/{userId}/{imageFileName}', 'Admin\UserController@removeImage')->name('users.remove_photo');
  Route::get('/users/daily_payment', 'Admin\UserController@getDailyPayment')->name('users.daily_payment');
  Route::get('/users/getListData', 'Admin\UserController@getListData')->name('getListData');
  Route::get('/users/delete/{agent_id}', 'Admin\UserController@deleteUser')->name('users.delete');
  Route::get('/users/checkemail/{email}', 'Admin\UserController@checkUserEmail')->name('users.checkemail');
  Route::post('/users/save', 'Admin\UserController@saveUser')->name('users.save');
  Route::get('/users/download_csv', 'Admin\UserController@downloadCSV')->name('users.download_csv');
  Route::get('/users/add_note', 'Admin\UserController@addNewNote')->name('users.add_note');
  Route::get('/users/note_list/{user_id}', 'Admin\UserController@noteList')->name('users.note_list');
  Route::get('/users/delete_note/{note_id}', 'Admin\UserController@deleteNote')->name('users.delete_note');
  Route::post('/users/upload_file', 'Admin\UserController@uploadUserFile')->name('users.file_upload');
  Route::post('/users/delete_uploaded_file', 'Admin\UserController@deleteUploadedFile')->name('users.delete_uploaded_file');

  Route::get('/change_admin_pass', [UserController::class, 'frmAdminPassChange'])->name('change_admin_pass.frm');
  Route::post('/change_admin_pass/save', [UserController::class, 'postChangeAdminPass'])->name('change_admin_pass.save');

  
   
});

Auth::routes();

