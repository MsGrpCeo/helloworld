<?php

namespace App\Http\Controllers\Admin;

use App\Common;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StoreTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    public function index() {
        return view('pages.agents.list');
    }

    public function editUser($userId) {
        $data = array('user_id'=>'NULL', 'email'=>'', 'name'=>'', 'password'=>'', 'company'=>'', 'phone'=>'', 'photo_url'=>'', 'date_created'=>'', 'act'=>0);
        if ( strtolower($userId) != 'null' ) {
            $record = app(User::class)->where('id', $userId)->first();
            $data['user_id'] = $record->id;
            $data['email'] = $record->email;
            $data['name'] = $record->name;
            $data['company'] = $record->company;
            $data['phone'] = $record->phone;
            $data['photo_url'] = Common::getBackendImg($record->photo_url);
            $data['date_created'] = $record->date_created == '' ? '' : date('Y-m-d H:i:s', $record->date_created);
            $data['act'] = $record->act;
        }
        return view('pages.agents.edit', $data);
    }
    /****
     * 
     *  *** getListData Action
     *  *** ActionName: getUserListData, RouteName: userlist.getListData, URI: /admin/userlist/getList
     */
    public function getListData()
    {
        $search = request()->get('search');
        $data_list = array();

        $sql = "SELECT * FROM users WHERE utype=0";
        if ( !is_null($search['value']) ) {
            $search_value = $search['value'];
            $sql .= " AND ( email LIKE '%{$search_value}%' OR name LIKE '%{$search_value}%' OR company LIKE '%{$search_value}%' OR phone LIKE '%{$search_value}%' )";
        }
        $sql .= " ORDER BY date_created DESC";
        $rows = DB::select($sql);
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$data_record = array();
				$data_record['id'] = $row->id;
				$data_record['name'] = $row->name;
                $data_record['email'] = $row->email;
				$data_record['company'] = $row->company;
				$data_record['phone'] = $row->phone;
				$data_record['photo_url'] = Common::getBackendImg($row->photo_url);
				$data_record['date_created'] = $row->date_created == '' ? '' : Common::convertUTCtoLocal(date('Y-m-d H:i:s', $row->date_created), request()->get('timezone'));
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

		for($i = $iDisplayStart; $i < $end; $i++) {
			
			$records["data"][] = array(
              '<span class="span-gender">'.($i+1).'</span>',
              '<span class="span-gender">'.$data_list[$i]['name'].'</span>',
              '<span class="span-gender">'.$data_list[$i]['email'].'</span>',
              '<span class="span-gender">'.$data_list[$i]['company'].'</span>',
              '<span class="span-gender">'.$data_list[$i]['phone'].'</span>',
              '<img src="'. $data_list[$i]['photo_url'].'" width="80px" height="80px" style="width: 100%!important; object-fit: contain;" />',
              '<span class="span-gender">'.$data_list[$i]['date_created'].'</span>',
              ($data_list[$i]['act'] == 0) ? '<span class="badge-ex badge-danger">Inactive</span>' : '<span class="badge-ex badge-green">Active</span>',
              
              '<span class="span-gender"><a href="'.route('admin.agents.delete', $data_list[$i]['id']).'" onclick="return confirm(\'Are you sure to delete?\')" class="delete-btn red" title="Delete"><i class="glyphicon glyphicon-trash"></i></a> &nbsp;
			  <a href="javascript:show_modal(\''.route("admin.agents.edit", $data_list[$i]['id']).'\');" class="edit-btn blue modal-trigger" title="Edit"><i class="icon-note"></i></a></span>'
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
        $user_id = $request->get('user_id');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $name = $request->get('name');
        $company = $request->get('company');
        $act = $request->get('act');

        if ( $user_id == 'NULL' ) {
            $record = new User();
            $record->date_created = time();
        }
        else {
            $record = app(User::class)->where('id', $user_id)->first();
        }
        $record->name = $name;
        $record->email = $email;
        $record->phone = $phone;
        $record->company = $company;
        if ( $request->file('photo') ) {
            $dest_path = base_path('/uploads/uploads_agents/');

            if ( !file_exists($dest_path) ) {
                \File::makeDirectory($dest_path, 0777, true);
            }

            if ( $record->photo_url != '' || is_null($record->photo_url) ) {
                $dest_path_ = base_path('/');
                if ( file_exists($dest_path_ . $record->photo_url) ) {
                    \File::delete($dest_path_ . $record->photo_url);
                }
            }
            $imageName = 'photo__'.$record->id.'_'.time().'.'.$request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move($dest_path, $imageName);
            $record->photo_url = 'uploads/uploads_agents/'.$imageName;
        }
        
        $record->act = $act == 'active' ? 1 : 0;
        
        if (!is_null($request->get('password'))) {
            $record->password = md5($request->get('password'));            
        }

        $record->save();

        return redirect()->route('admin.agents.list');
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
            $record = app(User::class)->where('email', $email)->first();
        }
        else {
            $record = app(User::class)->where('email', $email)->where('id', '<>', $user_id)->first();
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
        $record = app(User::class)->where('id', $user_id)->first();
        $dest_path = base_path('/');
        if ( $record->photo_url != '' || is_null($record->photo_url) ) {
            if ( file_exists($dest_path) ) {
                \File::delete($dest_path . $record->photo_url);
            }
        }
        $record->delete();
        return redirect()->route('admin.agents.list');
    }
    
    public function frmAdminPassChange() {
        $admin_user_id = \Auth::user()->id;
        return view('pages.agents.adminpass')->with(['message'=>'null']);
    }
    
    public function postChangeAdminPass(Request $request) {
        $admin_user_id = \Auth::user()->id;
        $record = app(User::class)->where('id', $admin_user_id)->first();
        $record->password = md5($request->password);
        $record->save();
        return redirect()->route('admin.agents.list');
        //return view('pages.agents.adminpass')->with(['message'=>'Admin user password has been changed suucessfully.']);
    }

    public function downloadCSV() {
        $sql = "SELECT * FROM tbl_UserTable WHERE 1=1 ORDER BY id ASC";
        $rows = DB::select($sql);
        $data_list = array();
        
        if (count($rows) > 0) {
			foreach($rows as $row) {
				$data_record = array();
                foreach( $row as $key=>$val ) {
                    if ( $key == 'utype' ) $data_record[$key] = $val == 1 ? 'Admin' : 'Agent';
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
            return redirect()->route('admin.agents.list');
        }
    }
}
