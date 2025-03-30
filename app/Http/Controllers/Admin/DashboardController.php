<?php

namespace App\Http\Controllers\Admin;

use App\Common;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ContactTable;
use App\Models\EquipmentTable;
use App\Models\FormTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function index() {
        $data = ['sales_agents'=>0, 'contacts'=>0, 'equipments'=>0, 'forms'=>0];
        $userRecords = app(User::class)->get();
        $data['sales_agents'] = count($userRecords);
        $contactRecords = app(ContactTable::class)->get();
        $data['contacts'] = count($contactRecords);
        $equipRecords = app(EquipmentTable::class)->get();
        $data['equipments'] = count($equipRecords);
        $formRecords = app(FormTable::class)->get();
        $data['forms'] = count($formRecords);


        return view('pages.admin.dashboardex', $data);
    }
}
