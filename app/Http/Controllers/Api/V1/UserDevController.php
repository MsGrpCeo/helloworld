<?php

namespace App\Http\Controllers\Api\V1;

use App\Common;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\PersonalAccessToken;
use \Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use App\Mail\SendGridMail;
use App\Models\ContactTable;
use App\Models\FormTable;
use App\Models\EquipmentTable;
use App\Models\EquipmentPhotoTable;
use App\Models\EquipmentSampleImagesTable;
use Illuminate\Support\Facades\DB;
use SendGrid\Mail\Mail;


class UserDevController extends Controller
{
  protected $connection = 'mysql2'; // Set the connection name
  public function __construct()
  {
    // // Optionally, you could set the connection for all database queries in the constructor
    DB::setDefaultConnection($this->connection);
    $this->middleware('dev.db_connection');
  }
  public function createUser(Request $request) {
    try {
      $validateUser = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required'
      ]);

      if($validateUser->fails()) {
        return response()->json([
          'status' => false,
          'message' => 'validation error',
          'errors' => $validateUser->errors()
        ], 401);
      }

      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'ss_tier' => 'basic',
        'exam_date' => 0,
        'daily_streak' => 0,
        'overal_percent' => "0",
        'd1_percent' => "0",
        'd2_percent' => "0",
        'd3_percent' => "0",
        'd4_percent' => "0",
        'd5_percent' => "0",
        'user_type' => 1
      ]);
      
      $jwt_token = null;
      $input = $request->only('email', 'password');

      if (!$jwt_token = JWTAuth::attempt($input)) {
        return response()->json([
          'status' => false,
          'message' => 'Email & Password does not match with our record.',
        ], 401);
      }

      return response()->json([
        'status' => true,
        'message' => 'User Created Successfully',
        'token' => $jwt_token,
        'user' => $user
      ], 200 );
    } catch (\Throwable $th) {
      return response()->json([
        'status' => false,
        'message' => $th->getMessage()
      ], 500);
    }
  }

  public function updateUser(Request $request) {
    try {
      // $FIELDS = ['name', 'password', 'ss_tier', 'reset_date', 'exam_date','daily_streak','overal_percent','d1_percent','d2_percent','d3_percent','d4_percent','d5_percent'];
      $FIELDS = ['name', 'password', 'reset_date', 'exam_date'];
      $validationFields = [];
      foreach ($FIELDS as $field) {
        $validationFields[$field] = 'sometimes';
      }

      $validateUser = Validator::make($request->all(), $validationFields);

      if($validateUser->fails()) {
        return response()->json([
          'status' => false,
          'message' => 'validation error',
          'errors' => $validateUser->errors()
        ], 401);
      }

      $curUser = $request->user();
      $isUpdatable = false;
      foreach ($FIELDS as $field) {
        if ($request->$field && $request->$field != $curUser->$field) {
          if($field == 'password') {
            $curUser->password = Hash::make($request->password);
          } else if($field == 'reset_date') {
            $curUser->reset_date = date('Y-m-d H:i:s', $request->reset_date);
          } else {
            $curUser->$field = $request->$field;
          }
          $isUpdatable = true;
        }
      }
      
      if($isUpdatable) {
        $curUser->save();
      }
      
      return response()->json([
        'status' => true,
        'message' => 'User Updated Successfully',
        'user' => $curUser
      ], 200 );
    } catch( \Throwable $th) {
      return response()->json([
        'status' => false,
        'message' => $th->getMessage()
      ], 500);
    }
  }

  public function loginUser(Request $request) {
    $ret = array('status'=>'error', 'data'=>[], 'token'=>null);
    try {
      $validateUser = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required'
      ]);

      if($validateUser->fails()) {
        // return response()->json([
        //   'status' => false,
        //   'message' => 'validation error',
        //   'errors' => $validateUser->errors()
        // ], 401);
        $ret['status'] = 'error';
      }

      $jwt_token = null;
      $input = $request->only('email', 'password');

      if ($jwt_token = JWTAuth::attempt($input)) {
        // return response()->json([
        //   'status' => false,
        //   'message' => 'Email & Password does not match with our record.',
        // ], 401);
        $user = User::where('email', $request->email)->first();
      
        if ($user->act == 0) {
          $ret['status'] = 'disabled_user';
        }
        else {
          $platform = request()->get('platform');
          $version = request()->get('version');
          if ($platform != '') {
            $user->version = $version;
            $user->platform = $platform;
            $user->save();
          }
          $user->last_login = time();
          $user->save();
          $ret['status'] = 'success';
          $ret['data'][0] = $user; 
          $ret['token'] = $jwt_token; 
        }
        // return response()->json([
        //   'status' => true,
        //   'message' => 'User Logged In Successfully',
        //   'token' => $jwt_token,
        //   'user' => $this->getUserData($user)
        // ], 200 );       
      }
      else {
        $ret['status'] = 'error';
      }

    } catch (\Throwable $th) {
      $user = User::where('email', $request->email)->first();
      if ($user->password == md5($request->password)) {
        $user->password = Hash::make($request->password);
        $platform = request()->get('platform');
        $version = request()->get('version');
        if ($platform != '') {
          $user->version = $version;
          $user->platform = $platform;
        }
        $user->last_login = time();
        $user->save();
        $jwt_token = null;
        $input = $request->only('email', 'password');
  
        if ($jwt_token = JWTAuth::attempt($input)) {
          $ret['status'] = 'success';
          $ret['data'][0] = $user; 
          $ret['token'] = $jwt_token; 
        }
        else {
          $ret['status'] = 'error';
        }
      }
      else {
        $ret['status'] = 'error';
      }
      // return response()->json([
      //   'status' => false,
      //   'message' => $th->getMessage()
      // ], 500);
    }
    return response()->json($ret);
  }

  public function registerUser(Request $request) {
    $ret = array('status'=>'error', 'data'=>[], );
    try {
      $email = request()->get('email');
      $name = request()->get('name');
      $company = request()->get('company');
      $phone = request()->get('phone');
      $password = request()->get('password');
        
      $date_created = time();

      $validateUser = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required'
      ]);

      if($validateUser->fails()) {
        // return response()->json([
        //   'status' => false,
        //   'message' => 'validation error',
        //   'errors' => $validateUser->errors()
        // ], 401);
        $ret['status'] = 'error';
      }
      $user = app(User::class)->where('email', $email)->first();
      if (is_null($user)) {
        $user = User::create([
          'name' => $name,
          'email' => $email,
          'password' => Hash::make($password),
        ]);
        $user->company = $company;
        $user->phone = $phone;
        $user->date_created = $date_created;
        $user->save();
        
        $ret['data'] = $user;

        $jwt_token = null;
        $input = $request->only('email', 'password');
  
        if (!$jwt_token = JWTAuth::attempt($input)) {
          $ret['status'] = 'error_after_register';
          // return response()->json([
          //   'status' => false,
          //   'message' => 'Email & Password does not match with our record.',
          // ], 401);
        }
        else {
          $ret['status'] = 'success';
        }
  
        // return response()->json([
        //   'status' => true,
        //   'message' => 'User Created Successfully',
        //   'token' => $jwt_token,
        //   'user' => $this->getUserData($user)
        // ], 200 );
      }
      else {
        $ret['status'] = 'already_exist';
      }

      
    } catch (\Throwable $th) {
      $ret['status'] = 'register_failed';
      // return response()->json([
      //   'status' => false,
      //   'message' => $th->getMessage()
      // ], 500);
    }
    return response()->json($ret);
  }

  public function editPushSetting(Request $request) {
    $ret = array('status'=>'success');
    try {
      $verify_key = request()->get('verify_key');
      $status = request()->get('status');

      if($verify_key == "8osh1se0yo2ng5") {
        $user = $request->user();
        if ($user) {
          $user->push_enabled = $status;
          $user->save();
        }
        else {
          $ret['status'] = "failed";
        }
      }
      else {
        $ret['status'] = "verify_error";
      }
    } catch (\Throwable $th) {
      $ret['status'] = "error";
    }
    return response()->json($ret);
  }

  public function editProfile(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $name = request()->get('name');
      $email = request()->get('email');
      $company = request()->get('company');
      $phone = request()->get('phone');

      $user = $request->user();
      if ($user) {
        $user->name = $name;
        $user->email = $email;
        $user->phone = $phone;
        $user->company = $company;
        $user->save();
        $ret['data'] = $user;
      }
      else {
        $ret['status'] = "failed";
      }
    } catch (\Throwable $th) {
      $ret['status'] = "error";
    }
    return response()->json($ret);
  }
  
  public function addProfilePhoto(Request $request) {
    $ret = array('status'=>'success', 'photo_url'=>null);
    try {
      $user = $request->user();
      if ( $request->file('userfile1') ) {
        $dest_path = base_path('/uploads/dev/uploads_photo/');

        if ( !file_exists($dest_path) ) {
          \File::makeDirectory($dest_path, 0777, true);
        }
        
        if ( $user->photo_url != '' || is_null($user->photo_url) ) {
          $dest_path_ = base_path('/');
          if ( file_exists($dest_path_ . $user->photo_url) ) {
            \File::delete($dest_path_ . $user->photo_url);
          }
        }
        $imageName = 'photo__'.$user->id.'_'.time().'.'.$request->file('userfile1')->getClientOriginalExtension();
        $request->file('userfile1')->move($dest_path, $imageName);
        $user->photo_url = 'uploads/dev/uploads_photo/'.$imageName;
        $user->save();
        // dd($request->file('userfile1')->getClientOriginalExtension(), $user);
        $ret['status'] = 'success';
        $ret['photo_url'] = $user->photo_url;
      }
      else {
        $ret['status'] = 'failed';
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'failed';
    }
    return response()->json($ret);
  }

  public function deleteAccount(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $user = $request->user();
      $user->act = 0;
      $user->save();
      $ret['status'] = 'success';
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'failed';
    }
    return response()->json($ret);
  }
  // sendgrid api
  public function sendVerifyEmail(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $email = request()->get('email');
      $code = mt_rand(100000,999999);
      $file = file_get_contents(base_path('/').'/NEW_verifycode.html', true);
      $text = str_replace('000000', $code, $file );

      // $data = ['to_address' => $email, 'subject'=> 'Verification Code for JPH Equipment app', 'code' => $code];

      // Mail::to('john@example.com')->send(new SendGridMail($data));
      $reset_pass_email = new Mail(); 
      $reset_pass_email->setFrom("noreply@jphequip.ca", "JPH Equipment");
      $reset_pass_email->setSubject("Verification Code for JPH Equipment app");
      $reset_pass_email->addTo($email, "JPH Equipment App User");
      
      $reset_pass_email->addContent("text/html", $text);
      $sendgrid = new \SendGrid(\Config::get('app.sendgrid_apikey'));
      $response = $sendgrid->send($reset_pass_email);

      $ret['status'] = 'success';
      $ret['code'] = $code;
    }
    catch ( \Throwable $th ) {
      $values['status'] = "failed";
      $ret['exception'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function changePassword(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $password = request()->get('new_pass');

      $user = $request->user();
      $user->password = Hash::make($password);
      $user->save();
      $ret['data'] = $user;

      // $jwt_token = null;
      // $input = array('email'=>$user->email, 'password'=>$password);
  
      // if (!$jwt_token = JWTAuth::attempt($input)) {
      //   $ret['status'] = 'error_with_jwt';
      // }
      // else {
      //   $ret['status'] = 'success';
      // }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'error';
    }
    return response()->json($ret);
  }

  public function uploadEquipImage(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $user = $request->user();
      $equipment_id = request()->get('equipment_id');
      $form_id = request()->get('form_id');
      $sample_id = intval(request()->get('sample_id'));

      if ( $request->file('userfile1') ) {
        $filenamedate = floor(microtime(true) * 1000);

        $file_sample_index = $sample_id;
        if ($sample_id == -1) {
          $file_sample_index = 999;
        }
        else {
          $record = app(EquipmentSampleImagesTable::class)->where('id', $sample_id)->first();
          if ($record) {
            $file_sample_index = $record->orderNum;
          }
        }
        $record = app(EquipmentPhotoTable::class)->where('agent_id', $user->id)->where('equipment_id', $equipment_id)->where('form_id', $form_id)->where('sample_id', $sample_id)->first();
        if (is_null($record)) {
          $record = new EquipmentPhotoTable();

          $dest_path = base_path('/uploads/dev/equip/');

          if ( !file_exists($dest_path) ) {
            \File::makeDirectory($dest_path, 0777, true);
          }
          
          if ( $record->photo_url != '' || is_null($record->photo_url) ) {
            $dest_path_ = base_path('/');
            if ( file_exists($dest_path_ . $record->photo_url) ) {
              \File::delete($dest_path_ . $record->photo_url);
            }
          }
          $photo_url = $user->id."_".$equipment_id."_".$form_id."_".$file_sample_index."_".$filenamedate.".".$request->file('userfile1')->getClientOriginalExtension();
          $request->file('userfile1')->move($dest_path, $photo_url);

          $record->agent_id = $user->id;
          $record->equipment_id = $equipment_id;
          $record->form_id = $form_id;
          $record->sample_id = $sample_id;
          $record->photo_url = 'uploads/dev/equip/'.$photo_url;
          $record->date_created = time();
          $record->save();
          // dd($request->file('userfile1')->getClientOriginalExtension(), $user);
          $ret['status'] = 'success';
          $ret['photo_url'] = $record->photo_url;
        }
        else {
          $ret['photo_url'] = $record->photo_url;
				  $ret['status'] = "success";
        }
      }
      else {
        $ret['photourl'] = "";
        $ret['status'] = 'failed';
        $ret['message'] = "invalid uploaded file content";
      }
    }
    catch ( \Throwable $th ) {
      $ret['photourl'] = "";
      $ret['status'] = 'failed_file_upload';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function getForms(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $user = $request->user();
      $sql = "SELECT f.*, e.equipment_name, e.equipment_image 
      FROM tbl_form f 
      JOIN tbl_equipment e ON f.equipment_id = e.id 
      WHERE f.agent_id = '$user->id' 
      ORDER BY f.date_updated DESC";

      $data = \DB::select($sql);
      if (count($data) > 0) {
        $ret['data'] = $data;
      }
      else {
        $ret['status'] = "nodata";
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'error';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function submitForm(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $user = $request->user();

      $equipment_id = is_null(request()->get('equipment_id')) ? 0 : request()->get('equipment_id');
      $contact_name = is_null(request()->get('contact_name')) ? '' : request()->get('contact_name');
      // $contact_name = mysqli_real_escape_string($link, $contact_name);
      $contact_phone = is_null(request()->get('contact_phone')) ? '' : request()->get('contact_phone');
      $contact_email = is_null(request()->get('contact_email')) ? '' : request()->get('contact_email');
      $machine_location = is_null(request()->get('machine_location')) ? '' : request()->get('machine_location');
      $serial = is_null(request()->get('serial')) ? '' : request()->get('serial');
      $year = is_null(request()->get('year')) ? '' : request()->get('year');
      $make = is_null(request()->get('make')) ? '' : request()->get('make');
      $model = is_null(request()->get('model')) ? '' : request()->get('model');
      $track_tire_size = is_null(request()->get('track_tire_size')) ? '' : request()->get('track_tire_size');
      $hours = is_null(request()->get('hours')) ? 0 : request()->get('hours');
      $stick_length = is_null(request()->get('stick_length')) ? '' : request()->get('stick_length');
      $pad_width = is_null(request()->get('pad_width')) ? '' : request()->get('pad_width');
      $epa_label = is_null(request()->get('epa_label')) ? '' : request()->get('epa_label');
      $quick_couple_type = is_null(request()->get('quick_couple_type')) ? '' : request()->get('quick_couple_type');
      $webasto_heater = is_null(request()->get('webasto_heater')) ? '' : request()->get('webasto_heater');
      $positive_air_shut_off = is_null(request()->get('positive_air_shut_off')) ? '' : request()->get('positive_air_shut_off');

      $value_json = "";
      if(!is_null(request()->get('value_json'))) {
        $value_json = request()->get('value_json');
      }
      $additional_info = '';
      if(!is_null(request()->get('additional_info'))) {
        $additional_info = request()->get('additional_info');
      }
      $value_base = '';
      if(!is_null(request()->get('value_base'))) {
        $value_base = request()->get('value_base');
      }
      $value_base0 = '';
      if(!is_null(request()->get('value_base0'))) {
        $value_base0 = request()->get('value_base0');
      }
      $value_a = is_null(request()->get('value_a')) ? 0 : request()->get('value_a');
      $value_b = is_null(request()->get('value_b')) ? 0 : request()->get('value_b');
      $value_c = is_null(request()->get('value_c')) ? 0 : request()->get('value_c');
      $value_d = is_null(request()->get('value_d')) ? 0 : request()->get('value_d');
      $value_e = is_null(request()->get('value_e')) ? 0 : request()->get('value_e');

      $dateCreated = time();

      $record = new FormTable();
      $record->agent_id = $user->id;
      $record->equipment_id = $equipment_id;
      $record->contact_name = $contact_name;
      $record->contact_phone = $contact_phone;
      $record->contact_email = $contact_email;
      $record->machine_location = $machine_location;
      $record->serial = $serial;
      $record->year = $year;
      $record->make = $make;
      $record->model = $model;
      $record->track_tire_size = $track_tire_size;
      $record->hours = $hours;
      $record->stick_length = $stick_length;
      $record->pad_width = $pad_width;
      $record->epa_label = $epa_label;
      $record->webasto_heater = $webasto_heater;
      $record->positive_air_shut_off = $positive_air_shut_off;
      $record->additional_info = $additional_info;
      $record->quick_couple_type = $quick_couple_type;
      $record->value_base0 = $value_base0;
      $record->value_base = $value_base;
      $record->value_a = $value_a;
      $record->value_b = $value_b;
      $record->value_c = $value_c;
      $record->value_d = $value_d;
      $record->value_e = $value_e;
      $record->value_json = $value_json;
      $record->date_created = $dateCreated;
      $record->date_updated = $dateCreated;
      $record->save();
      $ret['status'] = 'success';
      $sql_get = "select f.*,e.equipment_name, e.equipment_image from tbl_form f join tbl_equipment e on f.equipment_id = e.id where f.id = '$record->id'";
      $data = \DB::select($sql_get);
      if (count($data) > 0) {
        $ret['data'] = $data;
      }
      else {
        $ret['status'] = 'failed';  
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'failed';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function getEquipmentSamleSubImages(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {

      $equipment_id = request()->get('equipment_id');
      $subtype_id = request()->get('subtype');

      $records = app(EquipmentSampleImagesTable::class)->where('equipment_id', $equipment_id)->where('subtype_id', $subtype_id)->orderBy('orderNum', 'ASC')->orderBy('id', 'ASC')->get();

      if (count($records) == 0) {
        $ret['status'] = 'nodata';
      }
      else {
        $ret['data'] = $records->toArray();
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'nodata';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function getEquipmentSampleImages(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $equipment_id = $_REQUEST['equipment_id'];
      $subtype_id = 0;

      $records = app(EquipmentSampleImagesTable::class)->where('equipment_id', $equipment_id)->where('subtype_id', $subtype_id)->orderBy('orderNum', 'ASC')->orderBy('id', 'ASC')->get();

      if (count($records) == 0) {
        $ret['status'] = 'nodata';
      }
      else {
        $ret['data'] = $records->toArray();
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'error';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function getEquipmentList(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $records = app(EquipmentTable::class)->where('status', 1)->orderBy('sequence', 'ASC')->get();

      if (count($records) > 0) {
        $ret['data'] = $records->toArray();
      }
      else {
        $values['status'] = "nodata";
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'nodata';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function getContacts(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $search_key = request()->get('search_key');

      $records = array();
      if ($search_key != '') {
        $records = app(ContactTable::class)->where('name', 'LIKE', '%'.$search_key.'%')->orderBy('name', 'ASC')->get();
      }
      else {
        $records = app(ContactTable::class)->orderBy('name', 'ASC')->get();
      }

      if (count($records) > 0) {
        $ret['data'] = $records->toArray();
      }
      else {
        $values['status'] = "nodata";
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'nodata';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function checkExistEmail(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $email = request()->get('email');
      $record = app(User::class)->where('email', $email)->get();

      if (count($record) > 0) {
        $ret['status'] = "already_exist";
      }
      else {
        $ret['status'] = "not_exist";
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'nodata';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function getTime(Request $request) {
    return date('m/d/Y H:i:s', 1663790830);
  }

  public function contactUs(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $name = request()->get('name');
      $email = request()->get('email');
      $subject = request()->get('subject');
      $message = request()->get('description');

      $support_email = "support@jphequip.ca";

      $user_info = "<strong>Name </strong>: ".$name."<br>";
      $user_info = $user_info."<strong>Email </strong>: ".$email."<br>";
      $user_info = $user_info."<strong>Subject </strong>: ".$subject."<br>";
      $user_info = $user_info."<strong>Message </strong>: ".$message."<br>";

      if($user_info != "") {
        Common::SendHTMLMail($support_email,"New support request from JPH Equipment mobile app.",$user_info,"noreply@jphequip.ca","");
        $ret['status'] = "success";
      }
      else {
        $ret['status'] = "error";
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'error';
      $ret['message'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function frmSendLink(Request $request) {
    return view('pages.users_dev.sendlink');
  }

  public function sendLink(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $receiver_email = request()->get('email');
      $user = app(User::class)->where('email', $receiver_email)->first();
      if (is_null($user)) {
        $ret['status'] = 'wrong_email';
      }
      else {
        // $receiver_email = 'weblock9297@gmail.com';
        // $token=Str::random(60);
        $token=Common::quickRandom(512);
        $hash_token = $token;
        // $hash_token = Hash::make($token);
        app(PasswordReset::class)->where('email', $receiver_email)->delete();
        $record = new PasswordReset();
        $record->email = $receiver_email;
        $record->token = $hash_token;
        $record->created_at = time();
        $record->save();
        $ret['data'] = $record;

        $ret['link'] = route('api_dev.reset_pass', $hash_token);
        $link="<a href='".route('api_dev.reset_pass', $hash_token)."'>Click To Reset password</a>";
        $to = $receiver_email;
        $text = 'JPH Equipment has received a request to reset the password for your account. If you did not request to reset your password, please ignore this email. <br>'.$link.'';

        $reset_pass_email = new Mail(); 
        $reset_pass_email->setFrom("noreply@jphequip.ca", "JPH Equipment");
        $reset_pass_email->setSubject("Reset your JPH Equipment App Password");
        $reset_pass_email->addTo($receiver_email, "JPH Equipment App User");
        $reset_pass_email->addContent("text/html", $text);
        $sendgrid = new \SendGrid(\Config::get('app.sendgrid_apikey'));
        // $response = $sendgrid->send($reset_pass_email);

        $ret['data'] = compact('to', 'text', 'link', 'response');
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'wrong_email';
      $ret['exception'] = $th->getMessage();
    }
    return response()->json($ret);
  }

  public function frmResetPass($token) {
    $data = array('status'=>'success', 'message'=>'ok', 'email'=>'');
    try {
      $record = app(PasswordReset::class)->where('token', $token)->first();
      if (is_null($record)) {
        $data['status'] = 'failed';
        $data['message'] = "Invalid token";
      }
      else {
        $data['email'] = $record->email;
      }
    } catch (\Throwable $th) {
      $data['status'] = 'failed';
      $data['message'] = $th->getMessage();
    }
    return view('pages.users_dev.resetpass', $data);
  }
  public function resetPass(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $email = request()->get('email');
      $password = request()->get('password');
      $record = app(User::class)->where('email', $email)->first();
      $record->password = Hash::make($password);
      $record->save();

      app(PasswordReset::class)->where('email', $email)->delete();

    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'error';
      $ret['message'] = $th->getMessage();
    }
    return view('pages.users_dev.resetpass_result', $ret);
  }

  public function appleSignin(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $firstname = request()->get('firstname');
      $lastname = request()->get('lastname');
      $display_name = $firstname." ".$lastname;
      $social_identifier = request()->get('social_identifier');
      $social_login = request()->get('social_login');
      $email = request()->get('email');
      $platform = request()->get('platform');
      $version = request()->get('version');
      $player_id = is_null(request()->get('player_id')) ? '' : request()->get('player_id');

      $dateCreated = time();
      $record = app(User::class)->where('social_identifier', $social_identifier)->where('social_identifier', '<>', '')->get();
      if (count($record) > 0) {
        $ret['new_user'] = "no";
      }
      else {
        $ret['new_user'] = "yes";
        $record = new User();
        $record->firstname = $firstname;
        $record->lastname = $lastname;
        $record->display_name = $display_name;
        $record->email = $email;
        $record->password = Hash::make($social_identifier);
        $record->date_created = $dateCreated;
        $record->social_login = $social_login;
        $record->social_identifier = $social_identifier;
        if ($player_id != '') {
          $record->player_id = $player_id;
          $record->platform = $platform;
          $record->version = $version;
        }
        $record->save();
        $ret['data'] = $record;
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'error';
      $ret['message'] = $th->getMessage();
    }
    return "[".json_encode($ret)."]";
  }
  public function googleSignin(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $firstname = request()->get('firstname');
      $lastname = request()->get('lastname');
      $display_name = $firstname." ".$lastname;
      $social_identifier = request()->get('social_identifier');
      $social_login = request()->get('social_login');
      $email = request()->get('email');
      $platform = request()->get('platform');
      $version = request()->get('version');
      $player_id = is_null(request()->get('player_id')) ? '' : request()->get('player_id');
      $dateCreated = time();

      $record = app(User::class)->where('social_identifier', $social_identifier)->where('social_identifier', '<>', '')->get();
      if (count($record) > 0) {
        $ret['new_user'] = "no";
      }
      else {
        $ret['new_user'] = "yes";
        $record = new User();
        $record->firstname = $firstname;
        $record->lastname = $lastname;
        $record->display_name = $display_name;
        $record->email = $email;
        $record->password = Hash::make($social_identifier);
        $record->date_created = $dateCreated;
        $record->social_login = $social_login;
        $record->social_identifier = $social_identifier;

        if ($player_id != '') {
          $record->player_id = $player_id;
          $record->platform = $platform;
          $record->version = $version;
        }
        $record->save();
        $ret['data'] = $record;
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'error';
      $ret['message'] = $th->getMessage();
    }
    return "[".json_encode($ret)."]";
  }
  public function fbSignin(Request $request) {
    $ret = array('status'=>'success', 'data'=>null);
    try {
      $firstname = request()->get('firstname');
      $lastname = request()->get('lastname');
      $fb_id = request()->get('id');
      $email = request()->get('email');
      $birthday = request()->get('birthday');

      $dateCreated = time();

      $record = app(User::class)->where('email', $email)->first();
      if (count($record) == 0) {
        $record = new User();
        $record->email = $email;
        $record->password = Hash::make($fb_id);
        $record->firstname = $firstname;
        $record->lastname = $lastname;
        $record->birthday = $birthday;
        $record->date_created = $dateCreated;
        $record->facebook_user = 1;
        $record->save();

        $ret['data'] = $record;
      }
      else {
        $ret['status'] = 'error';
        $ret['message'] = 'already_exist';
      }
    }
    catch ( \Throwable $th ) {
      $ret['status'] = 'error';
      $ret['message'] = $th->getMessage();
    }
    return "[".json_encode($values)."]";
  }

  public function currentUser(Request $request) {
    try {
      // $old_pass = request()->get('old_pass');
      // $new_pass = request()->get('new_pass');
      $curUser = $request->user();
      return response()->json([
        'status' => true,
        'message' => 'Current User',
        'user' => $curUser
      ], 200 );
    } catch( \Throwable $th) {
      return response()->json([
        'status' => false,
        'user' => $request->user(),
        'message' => $th->getMessage()
      ], 500);
    }
  }

  public function logoutUser(Request $request) {
    try {
      // $request->user()->currentAccessToken()->delete();
      Auth::guard('api')->logout();
      return response()->json(['status' => true, 'message' => 'Successfully logged out!'], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => false,
        'message' => $th->getMessage()
      ], 500);
    }
  }

  public function logoutAllUsers(Request $request) {
    try {
      // Revoke all tokens...
      $request->user()->tokens()->delete();
      return response()->json(['status' => true, 'message' => 'Successfully logged out!'], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => false,
        'message' => $th->getMessage()
      ], 500);
    }
  }
  /*
    public function deleteAccount(Request $request) {
      $uRecord = $request->user();
      $uRecord->tokens()->delete();
      
      app(Answer::class)->where('uid', $uRecord->id)->delete();
      $uRecord->delete();
      
      return response()->json(['status' => true, 'message' => 'Successfully deleted!'], 200);
    }
  */

  public function forgotPasswordOld(Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    
    return $status === Password::RESET_LINK_SENT ? response()->json([
      'status' => true,
      'message' => __($status)
    ], 200 ) : response()->json([
      'status' => false,
      'message' => __($status),
    ], 500);
  }

  public function getPerformanceAnalysisData(Request $request) {
    try {
      $validateUser = Validator::make($request->all(), [
        'email' => 'required|email',
      ]);

      if($validateUser->fails()) {
        return response()->json([
          'status' => false,
          'message' => 'validation error',
          'errors' => $validateUser->errors()
        ], 401);
      }
    } catch (\Throwable $th) {
        
    }
  }

  public function processAppleServerNotification(Request $request) {
    try {
      // if ($request->hasHeader("Authorization")) {
      //   $token = $request->header("Authorization");
      //   // $isDailyAnswerExist = Answer::where([['uid', $uid], ['date', $date], ['exam_type', 0]])->exists();
      //   if (!PersonalAccessToken::where([['id', 2], ['token', $token]])->exists()) {
      //     return response()->json([
      //       'success'=> false,
      //       "message"=> "Authorization token is invalid"
      //     ], 403);
      //   }
      // } else {
      //   return response()->json([
      //     'success'=> false,
      //     "message"=> "Authorization token not found"
      //   ], 403);
      // }
      /**
       * 
       * 
       */
      
      $event = $request->event;
      ///////////// Should be uncommented for production /////////////////
      /*
      if($event['environment'] != "PRODUCTION") {
          return response()->json([
              'message' => 'Bad server notification'
          ], 500);
      }
      */
      $uid = $event['app_user_id'];
      $user = User::where('id', $uid)->first();
      $ss_tier = "basic";

      switch ($event['type']) {
        case "INITIAL_PURCHASE":
        case "RENEWAL":
        case "UNCANCELLATION":
        case "NON_RENEWING_PURCHASE":
          if($event['product_id']=="bbp_monthly") {
            $ss_tier = "monthly";
          }
          if($event['product_id'] == "bbp_yearly") {
            $ss_tier = "yearly";
          }
          $user->ss_tier = $ss_tier;
          $user->save();
          break;
        case "CANCELLATION":
        case "EXPIRATION":
          $user->ss_tier = $ss_tier;
          $user->save();
        default:
          break;
      }

        return response()->json();
    } catch (\Throwable $th) {
      return response()->json([
        'message' => $th->getMessage()
      ], 500);
    }
  }

  public function getTest(Request $request) {
    return response()->json([
      'message' => $request->all()
    ]);
  }

  public function postTest(Request $request) {
    return response()->json([
      'message' => $request->all()
    ]);
  }
}
