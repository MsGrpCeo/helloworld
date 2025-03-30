<?php

namespace App\Http\Controllers\Auth;

use App\Models\PasswordReset;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Validator;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = '/home';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }
    
  public function login(Request $request) {
    if (session()->has('error')) {
      session()->forget('error');
    }
    $email = strtolower($request->email);
    // $request->validate([            
    //   'email'=>'required|email:users',
    //   'password'=>'required|min:8|max:50'
    // ]);

    $user = User::where('email','=',$request->email)->first();
    if($user){
      if ($user->user_type == 2) {
        session()->flash('status', 'failed');
        session()->flash('message', 'Invalid user!');
        return back()->with('fail','Invalid user!');
      }
      // dd($user, Hash::check($request->password, $user->password));
      try {
        if(Hash::check($request->password, $user->password)){
          \Auth::login($user);
          $request->session()->regenerate();
  
          $this->clearLoginAttempts($request);
          if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
          }
          return redirect()->route('admin.home.list');
        } else {
          session()->flash('status', 'failed');
          session()->flash('message', 'Password not match!');
          return back()->with('fail','Password not match!');
        }
      } catch (\Throwable $th) {
        if ($user->password == md5($request->password)) {
          $user->password = Hash::make($request->password);
          $user->save();
          \Auth::login($user);
          $request->session()->regenerate();

          $this->clearLoginAttempts($request);
          if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
          }
          return redirect()->route('admin.home.list');
        }
        session()->flash('status', 'failed');
        session()->flash('message', 'Password not match!');
        return back()->with('fail','Password not match!');
      }
      
    } else {
      return back()->with('fail','This email is not register.');
    } 

    
    // $this->incrementLoginAttempts($request);
    session()->put('error', "Invalid Login Credentials");
    session()->put('email', $email);
    session()->put('password', $request->password);
    // dd(session()->get('error'));
    return redirect()->back();
    // return $this->sendFailedLoginResponse($request);
    // throw ValidationException::withMessages([
    //     "message" => "Invalid Login Credentials",
    // ]);
  }
  public function logout(Request $request) {   
    try{
      $this->guard()->logout();

      $request->session()->invalidate();

      $request->session()->regenerateToken();
      if (session()->has('error')) {
        session()->forget('error');
      }
    }
    catch(\Exception $exp) {

    }
    
    return redirect('/');
  }

  public function resetpasswordview(Request $request) {
    return view('auth.passwords.forgot_reset')->with('token', $request->token);
  }
  public function resetpassword(Request $request) {

    try {
      $validateUser = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
        'token' => 'required',
      ]);

      if($validateUser->fails()) {
        return response()->json([
          'status' => false,
          'message' => 'validation error',
          'errors' => $validateUser->errors()
        ], 401);
      }
      $resetRecord = PasswordReset::firstWhere('email', $request->email);

      if($resetRecord && Hash::check($request->token, $resetRecord->token)) {
        $record = app(User::class)->where('email', $request->email)->first();
        $record->password = Hash::make($request->password);
        $record->save();
        return view('auth.passwords.password_reset_success')->with('message', "Password has been successfully changed. Please sign in again.");
        // return response()->json(array('message'=>'Updated successfully.'));
      } else {
        return view('auth.passwords.password_reset_success')->with('message', "Token is invalid. Please try again.");
      }
    } catch (\Throwable $th) {
      //throw $th;
      return view('auth.passwords.password_reset_success')->with('message', "Something went wrong. Please try again later.");
    }
  }
}
