<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    return redirect()->route('quizlist');
    // return view('home');
  }

  public function frmAdminPassChange() {
    $admin_user_id = \Auth::user()->id;
    return view('adminpass')->with(['message'=>'null']);
  }
  public function postChangeAdminPass(Request $request) {
    $record = app(User::class)->where('id', \Auth::user()->id)->first();
    $record->password = Hash::make($request->password);
    $record->save();
    return response()->json(array('message'=>'Updated successfully.'));
  }
}
