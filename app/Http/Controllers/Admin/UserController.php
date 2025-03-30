<?php

namespace App\Http\Controllers\Admin;

use App\Common;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotesTable;
use App\Models\AdminAuth;
use App\Models\StoreTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    public function index() {
        return view('pages.users.list');
    }
    public function editUser($userId) {
        $data = array('user_id'=>'NULL', 'email'=>'', 'password'=>'', 'name'=>'', 'country_code'=>'', 'phone'=>'', 'dob'=>'', 'occupation'=>'','address'=>'', 'personality1'=>'', 'personality2'=>'', 'personality3'=>'', 'gender'=>0, 'interests'=>'', 'about_me'=>'', 'count_ghost'=>'', 'swipe_count'=>0, 'swipe_count2'=>0, 'search_age_min'=>18, 'search_age_max'=>40, 'dam'=>'', 'member_since'=>'', 'recency'=>'', 'platform'=>'', 'version'=>'', 'photo_url'=>Common::getBackendImg(), 'act'=>0);
        $data['interest_list'] = Common::interestsList();
        $data['gender_list'] = Common::genderList();

        if ( strtolower($userId) != 'null' ) {
            $record = app(UserTable::class)->where('id', $userId)->first();
            $data['user_id'] = $record->id;
            $data['email'] = $record->email;
            $data['password'] = '';
            $data['name'] = $record->name;
            $data['country_code'] = $record->country_code;
            $data['phone'] = $record->phone;
            $data['dob'] = $record->dob;
            $data['occupation'] = $record->occupation;
            $data['address'] = $record->address;
            $data['personality1'] = $record->personality1;
            $data['personality2'] = $record->personality2;
            $data['personality3'] = $record->personality3;
            $data['gender'] = $record->gender;
            $data['interests'] = ($record->interests == '') ? [] : explode(',', $record->interests);
            $data['about_me'] = $record->about_me;
            $data['count_ghost'] = $record->count_ghost;
            $data['swipe_count'] = $record->swipe_count;
            $data['swipe_count2'] = $record->swipe_count2;
            $data['search_age_min'] = $record->search_age_min;
            $data['search_age_max'] = $record->search_age_max;
            $data['dam'] = $record->dam;
            $data['recency'] = date('Y-m-d H:i:s', $record->recency);
            $data['platform'] = $record->platform;
            $data['version'] = $record->version;

            $photos = explode(',', $record->photo_url);
            $photo_list = [];
            foreach($photos as $photo) {
                $photo_list[] = $photos != '' ? Common::getOnlineImgURL($photos[0]) : Common::getBackendImg();
            }
            $data['photo_list'] = $photo_list;
            $data['photo_url'] = Common::getBackendImg($record->photo_url);
            $data['member_since'] = $record->member_since;
            $data['act'] = $record->act*1;
            // dd($childs_list);
        }
        // dd($data);
        return view('pages.users.edit', $data);
    }
    public function getPhotoList($userId) {
        $ret = [];
        if ( strtolower($userId) != 'null' ) {
            $record = app(UserTable::class)->where('id', $userId)->first();
            $photos = explode(',', $record->photo_url);
            $photo_list = [];
            foreach($photos as $photo) {
                $photo_data = array();
                $photo_data['filename'] = $photo;
                $photo_data['url']= $photo != '' ? Common::getOnlineImgURL($photo) : Common::getBackendImg();
                $photo_list[] = $photo_data;
            }
            $ret = $photo_list;
        }
        return response()->json($ret);
    }
    public function removeImage($userId, $imageFileName) {
        if ( strtolower($userId) != 'null' ) {
            $record = app(UserTable::class)->where('id', $userId)->first();
            $photos = explode(',', $record->photo_url);
            $pos = array_search($imageFileName, $photos);

            if ($pos !== false) {
                // Remove from array
                unset($photos[$pos]);
            }

            $photo_list = [];
            foreach($photos as $photo) {
                $photo_data = array();
                $photo_data['filename'] = $photo;
                $photo_data['url']= $photo != '' ? Common::getOnlineImgURL($photo) : Common::getBackendImg();
                $photo_list[] = $photo_data;
            }
            $ret = $photo_list;
            $record->photo_url = implode(',', $photos);
            $record->save();
        }
        return response()->json('ok');
    }
    public function getDailyPayment() {
        $pay_frequency = request()->get('pay_frequency');
        $pay_after_tax = request()->get('pay_after_tax');
        $daily_fee_percent = request()->get('daily_fee_percent');
        $ret = Common::calcDailyPayment($pay_frequency, $pay_after_tax, $daily_fee_percent);
        return response()->json($ret);
    }
    /****
     * 
     *  *** getListData Action
     *  *** ActionName: getUserListData, RouteName: userlist.getListData, URI: /admin/userlist/getList
     */
    public function getListData() {
        $search = request()->get('search');
        $data_list = array();

        $sql = "SELECT * FROM tbl_user WHERE 1=1 "; 
        if ( !is_null($search['value']) ) {
            $search_value = $search['value'];
            $sql = "SELECT * FROM tbl_user WHERE 1=1 AND ( email LIKE '%{$search_value}%' OR name LIKE '%{$search_value}%' OR address LIKE '%{$search_value}%' OR occupation LIKE '%{$search_value}%' OR member_since LIKE '%{$search_value}%' OR platform LIKE '%{$search_value}%' OR dam LIKE '%{$search_value}%' )";
        }

        //$order_str = " ORDER BY FIELD(payment_status, -2, -4, -3, -1, 0, 1, 2, 3, 4), last_pay_amount DESC, statement_uploaded_date DESC, member_since DESC";
        $order_str = " ORDER BY id DESC";

        $sql .= $order_str;
        // dd($sql);
        $rows = DB::select($sql);
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$data_record = array();
				$data_record['id'] = $row->id;
                $data_record['email'] = $row->email;
                $data_record['name'] = $row->name;
                $photos = explode(',', $row->photo_url);
				$data_record['photo'] = count($photos) > 0 ? ($photos[0] != '' ? Common::getOnlineImgURL($photos[0]) : Common::getBackendImg()) : Common::getBackendImg();
				$data_record['dob'] = $row->dob;
				$data_record['occupation'] = $row->occupation;
				$data_record['address'] = $row->address;
				$data_record['gender'] = $row->gender;
				$data_record['count_ghost'] = $row->count_ghost;
				$data_record['dam'] = $row->dam;
				$data_record['member_since'] = date('Y-m-d H:i:s', strtotime($row->member_since));
				$data_record['recency'] = date('Y-m-d H:i:s', $row->recency);
				$data_record['platform'] = $row->platform;
				$data_record['version'] = $row->version;
				$data_record['act'] = $row->act;

				$data_list[] = $data_record;
			}
		}

    	$iTotalRecords = count($data_list);
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);

		$records = array();
		$records["data"] = array(); 

		$end = $iDisplayStart + $iDisplayLength;
		$end = $end > $iTotalRecords ? $iTotalRecords : $end;

        $genders = Common::genderList();
		for($i = $iDisplayStart; $i < $end; $i++) {
			
			$records["data"][] = array(
              ($i+1),
              $data_list[$i]['email'],
              $data_list[$i]['name'],
              '<img src="'. $data_list[$i]['photo'].'" width="80px" height="80px" style="object-fit: center;" />',
              $data_list[$i]['dob'],
              $data_list[$i]['occupation'],
              (strlen($data_list[$i]['address']) > 30 ? (substr($data_list[$i]['address'], 0, 30).'...') : $data_list[$i]['address']),
              $genders[$data_list[$i]['gender']],
              ($data_list[$i]['count_ghost'] == 0 ? '' : $data_list[$i]['count_ghost']),
              $data_list[$i]['dam'],
              $data_list[$i]['member_since'],
              $data_list[$i]['recency'],
              $data_list[$i]['platform'],
              $data_list[$i]['version'],
              ($data_list[$i]['act'] == 0) ? '<span class="badge-ex badge-danger">Inactive</span>' : '<span class="badge-ex badge-green">Active</span>',
              
            //   '<span class="span-gender"><a href="'.route('admin.users.delete', $data_list[$i]['id']).'" onclick="return confirm(\'Are you sure to delete?\')" class="delete-btn red" title="Delete"><i class="glyphicon glyphicon-trash"></i></a> &nbsp;
              '<span class="span-gender"><a href="javascript:show_modal(\''.route("admin.users.edit", $data_list[$i]['id']).'\');" class="edit-btn blue modal-trigger" title="Edit"><i class="icon-note"></i></a> &nbsp;
              <a href="'.route('admin.users.delete', $data_list[$i]['id']).'" onclick="return confirm(\'Are you sure to delete?\')" class="delete-btn red" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
              </span>'
			);
		}

		if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
			$records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
			$records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;

		return response()->json($records);
    }
    public function saveUser(Request $request) {
        // dd($request->input());
        $user_id = $request->get('user_id');
        $email = $request->get('email');
        $name = $request->get('name');
        $dob = $request->get('dob');
        $address = $request->get('address');
        $gender = $request->get('gender');
        $occupation = $request->get('occupation');
        $personality1 = $request->get('personality1');
        $personality2 = $request->get('personality2');
        $personality3 = $request->get('personality3');
        $count_ghost = $request->get('count_ghost');
        $swipe_count = $request->get('swipe_count');
        $swipe_count2 = $request->get('swipe_count2');
        $about_me = $request->get('about_me');
        $search_age_min = $request->get('search_age_min');
        $search_age_max = $request->get('search_age_max');
        $dam = $request->get('dam');
        $password = $request->get('password');
        $act = $request->get('act');

        if ( $user_id == 'NULL' ) {
            $record = new UserTable();
        }
        else {
            $record = app(UserTable::class)->where('id', $user_id)->first();
        }
        $genders = Common::genderList();
        $record->email = $email;
        $record->name = $name;
        $record->dob = is_null($dob) ? '' : $dob;
        $record->address = is_null($address) ? '' : $address;
        $record->gender = is_null($gender) ? '' : $gender;
        $record->occupation = is_null($occupation) ? '' : $occupation;
        $record->personality1 = is_null($personality1) ? '' : $personality1;
        $record->personality2 = is_null($personality2) ? '' : $personality2;
        $record->personality3 = is_null($personality3) ? '' : $personality3;
        $record->count_ghost = is_null($count_ghost) ? '' : $count_ghost;
        $record->swipe_count = is_null($swipe_count) ? '' : $swipe_count;
        $record->swipe_count2 = is_null($swipe_count2) ? '' : $swipe_count2;
        $record->about_me = is_null($about_me) ? '' : $about_me;
        $record->search_age_min = is_null($search_age_min) ? '' : $search_age_min;
        $record->search_age_max = is_null($search_age_max) ? '' : $search_age_max;
        $record->dam = is_null($dam) ? '' : $dam;
        $record->act = $act == 'active' ? 1 : 0;
        if ($user_id != 'NULL' && !is_null($record->password) && !is_null($password)) {
            $record->password = md5($password);            
        }
        
        if ($user_id == 'NULL' && $password != '' && !is_null($password)) {
            $record->password = md5($password);            
        }

        $record->save();

        return redirect()->route('admin.users.list');
    }
    /****
     * 
     *  *** Check Email for user Action
     *  *** ActionName: checkUserEmail, RouteName: store.user.checkemail, URI: /admin/userlist/checkemail/{email}
     */
    public function checkUserEmail($email) {
        $user_id = 'NULL';
        if ( request()->has('user_id') ) $user_id = request()->get('user_id');
        if ( $user_id == 'NULL' ) {
            $record = app(UserTable::class)->where('email', $email)->first();
        }
        else {
            $record = app(UserTable::class)->where('email', $email)->where('id', '<>', $user_id)->first();
        }
        // $record = app(UserTable::class)->where('email', $email)->first();
        if ( $record ) return response()->json([ 'status'=>'exists']);
        return response()->json([ 'status'=>'none']);
    }
    /****
     * 
     *  *** Delete User Action
     *  *** ActionName: deleteUser, RouteName: store.user.delete, URI: /admin/userlist/delete/{user_id}
     */
    public function deleteUser($user_id) {
        $record = app(UserTable::class)->where('id', $user_id)->first();
        $record->delete();
        return redirect()->route('admin.users.list');
    }
    
    public function frmAdminPassChange() {
        $admin_user_id = \Auth::user()->id;
        return view('pages.users.adminpass')->with(['message'=>'null']);
    }
    
    public function postChangeAdminPass(Request $request) {
        $admin_user_id = \Auth::user()->id;
        $record = app(User::class)->where('id', $admin_user_id)->first();
        // $record->password = md5($request->password);
        $record->password = Hash::make($request->password);
        // $record->password_str = $request->password;
        $record->save();
        return redirect()->back()->with('message', 'Changed password successfully.');
        // return redirect()->route('admin.restaurant.list');
        //return view('pages.users.adminpass')->with(['message'=>'Admin user password has been changed suucessfully.']);
    }

    public function downloadCSV() {
        $sql = "SELECT * FROM tbl_user WHERE 1=1 ORDER BY id DESC";
        $rows = DB::select($sql);
        $statues = ['Inactive', 'Active'];
        $data_list = array();

        if (count($rows) > 0) {

			foreach($rows as $row) {
				$data_record = array();
                foreach( $row as $key=>$val ) {
                    if ($key == 'act') $data_record[$key] = $statues[$val];
                    else if ($key == 'date_created' || $key == 'recency') $data_record[$key] = date('Y-m-d H:i:s', $val);
                    else $data_record[$key] = $val;
                }
				$data_list[] = $data_record;
			}
		}
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=".request()->segment(2)."_".date("Y-m-d H:i:s")."_csv.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        if ( count($data_list) > 0 ) {
            $columns = [];
            foreach( $data_list[0] as $key=>$val ) {
                $columns[] = Common::makeFieldToCsvHeader($key);
            }
            
            $callback = function() use ($data_list, $columns)
            {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
        
                foreach($data_list as $row) {
                    $csv_row = array();
                    foreach( $row as $key => $val ) {
                        $csv_row[] = $val;
                    }
                    $csv_row = mb_convert_encoding($csv_row,"ISO-8859-1", "UTF-8");
                    fputcsv($file, $csv_row);
                }
                fclose($file);
            };
            return \Response::stream($callback, 200, $headers);
        }
        else {
            return redirect()->route('admin.users.list');
        }
    }
    function addNewNote() {
        $user_id = request()->get('user_id');
        $note_id = request()->get('note_id');
        $note = request()->get('note');
        if ( strtolower($note_id) == 'null' ) {
            $record = new UserNotesTable();
            $record->user_id = $user_id;
            $record->creator_id = \Auth::user()->id;
            $record->date_created = time();
            $record->date_updated = time();
        }
        else {
            $record = app(UserNotesTable::class)->where('id', $note_id)->first();
        }
        $record->note = $note;
        $record->date_updated = time();
        $record->save();
        return response()->json('ok');
    }
    function noteList($user_id) {
        $sql = "SELECT n.*, a.name who FROM tbl_user_notes n JOIN tbl_adminauth a ON a.id = n.creator_id WHERE n.user_id=".$user_id." ORDER BY date_created DESC";
        $records = \DB::select($sql);

        return response()->json($records);
    }
    function deleteNote($note_id) {
        $record = app(UserNotesTable::class)->where('id', $note_id)->first();
        $record->delete();
        return response()->json(['result'=>200]);
    }
}
