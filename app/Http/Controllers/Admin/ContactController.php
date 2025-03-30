<?php

namespace App\Http\Controllers\Admin;

use App\Common;
use App\Http\Controllers\Controller;
use App\Models\ContactTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index() {
        return view('pages.contact.list');
    }
    public function editContact($userId) {
        $data = array('user_id'=>'NULL', 'email'=>'', 'name'=>'', 'phone'=>'');
        if ( strtolower($userId) != 'null' ) {
            $record = app(ContactTable::class)->where('id', $userId)->first();
            $data['user_id'] = $record->id;
            $data['email'] = $record->email;
            $data['name'] = $record->name;
            $data['phone'] = $record->phone;
        }
        return view('pages.contact.edit', $data);
    }
    /****
     * 
     *  *** getListData Action
     *  *** ActionName: getUserListData, RouteName: userlist.getListData, URI: /admin/userlist/getList
     */
    public function getListData() {
        $search = request()->get('search');
        $data_list = array();

        $sql = "SELECT * FROM tbl_contact WHERE 1=1 "; 
        if ( !is_null($search['value']) ) {
            $search_value = $search['value'];
            $sql .= " AND ( email LIKE '%{$search_value}%' OR name LIKE '%{$search_value}%' OR phone LIKE '%{$search_value}%' )";
        }

        $order_str = " ORDER BY id DESC";
        $sql .= $order_str;
        // dd($sql);
        $rows = DB::select($sql);
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$data_record = array();
				$data_record['id'] = $row->id;
                $data_record['name'] = $row->name;
                $data_record['email'] = $row->email;
				$data_record['phone'] = $row->phone;

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
              ($i+1),
              $data_list[$i]['name'],
              $data_list[$i]['email'],
              $data_list[$i]['phone'],
              '<span class="span-gender"><a href="javascript:show_modal(\''.route("admin.contact.edit", $data_list[$i]['id']).'\');" class="edit-btn blue modal-trigger" title="Edit"><i class="icon-note"></i></a> &nbsp;&nbsp;
              <a href="'.route('admin.contact.delete', $data_list[$i]['id']).'" onclick="return confirm(\'Are you sure to delete?\')" class="delete-btn red" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
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
    public function saveData(Request $request) {
        $user_id = $request->get('user_id');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $name = $request->get('name');

        if ( $user_id == 'NULL' ) {
            $record = new ContactTable();
        }
        else {
            $record = app(ContactTable::class)->where('id', $user_id)->first();
        }
        $record->name = $name;
        $record->email = $email;
        $record->phone = $phone;
        $record->save();

        return redirect()->route('admin.contact.list');
    }
    public function deleteContact($id) {
        $record = app(ContactTable::class)->where('id', $id)->first();
        if ($record) $record->delete();
        return redirect()->route('admin.contact.list');
    }
    public function downloadCSV() {
        $sql = "SELECT * FROM tbl_contact WHERE 1=1 ORDER BY id DESC";
        $rows = DB::select($sql);
        $statues = ['Inactive', 'Active'];
        $data_list = array();

        if (count($rows) > 0) {
            foreach($rows as $row) {
				$data_record = array();
                foreach( $row as $key=>$val ) {
                    if ($key == 'act') $data_record[$key] = $statues[$val];
                    else if ($key == 'date_created' || $key == 'member_since') $data_record[$key] = date('Y-m-d H:i:s', $val);
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
            return redirect()->route('admin.manage.resources.list');
        }
    }
}
