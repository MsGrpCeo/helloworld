<?php

namespace App\Http\Controllers\Admin;

use App\Common;
use App\Http\Controllers\Controller;
use App\Models\UserTable;
use App\Models\EquipmentTable;
use App\Models\EquipmentSampleImagesTable;
use App\Models\AdminAuth;
use App\Models\StoreTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Intervention\Image\ImageManager;

class EquipmentController extends Controller
{
    private $form_types;
    public function __construct() {
        $this->form_types = ['0'=> 'Normal', '1'=> 'Excavator', '2'=> 'Wheel loader', '3'=>'Dozer',
            '4'=>'Motor Grader',
            '5'=>'Compactor',
            '6'=>'Telehandler',
            '7'=>'Cranes',
            '8'=>'Drill Rigs',
            '9'=>'Skid Steer',
            '10'=>'Generators/Power Systesm',
            '11'=>'Trucks',
            '12'=>'Trailers',
            '13'=>'Agriculture-Tractors & Combines',
            '14'=>'Log Loader',
            '15'=>'Delimer',
            '16'=>'Processor',
            '17'=>'Skidder',
            '18'=>'Feller Buncher',
            '19'=>'Attachments',
            '20'=>'Attachments',
        ];
    }

    public function index() {
        return view('pages.equipment.list');
    }
    public function editUser($transaction_id) {
        $data = array('transaction_id'=>'NULL', 'equipment_name'=>'', 'equipment_image'=>Common::getBackendImg(), 'form_type'=>0, 'status'=>0, 'created_at'=>'');
        if ( strtolower($transaction_id) != 'null' ) {
            $record = app(EquipmentTable::class)->where('id', $transaction_id)->first();
            $data['transaction_id'] = $record->id;
            $data['equipment_name'] = $record->equipment_name;
            $data['form_type'] = $record->form_type;
            $data['created_at'] = is_null($record->created_at) || $record->created_at == '' || $record->created_at == 0 ? '' : date('Y-m-d H:i:s', $record->created_at);
            $data['status'] = $record->status;
            $data['sequence'] = $record->sequence;
            $data['equipment_image'] = Common::getBackendImg($record->equipment_image);
        }
        $data['form_types'] = $this->form_types;
        return view('pages.equipment.edit', $data);
    }
    
    public function getListData() {
        $search = request()->get('search');
        $data_list = array();

        $sql = "SELECT * FROM tbl_equipment WHERE 1=1 ";
        if ( !is_null($search['value']) ) {
            $search_value = $search['value'];
            $sql .= " AND ( equipment_name LIKE '%{$search_value}%' )";
        }
        $order_str = " ORDER BY sequence ASC";

        $sql .= $order_str;
        $rows = DB::select($sql);
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$data_record = array();
				$data_record['id'] = $row->id;
				$data_record['equipment_name'] = $row->equipment_name;
				$data_record['form_type'] = $this->form_types[$row->form_type];
				$data_record['status'] = $row->status;
				$data_record['created_at'] = is_null($row->created_at) || $row->created_at == '' || $row->created_at == 0 ? '' : date('Y-m-d H:i:s', $row->created_at);
				$data_record['sequence'] = $row->sequence;
				$data_record['equipment_image'] = Common::getBackendImg($row->equipment_image);

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
              ($i + 1),
              '<a href="'.route('admin.equipment.sample_images', $data_list[$i]['id']).'" >'.$data_list[$i]['equipment_name'].'</a>',
              '<img src="'. $data_list[$i]['equipment_image'].'" width="80px" height="80px" style="width: 100%!important; object-fit: contain;" />',
              $data_list[$i]['form_type'],
              $data_list[$i]['created_at'],
              ($data_list[$i]['status'] == 0) ? '<span class="badge-ex badge-danger">Inactive</span>' : '<span class="badge-ex badge-green">Active</span>',
              ($i == $end-1) ? 
                ($iTotalRecords == 1 ? '<span class="span-gender">
                <a href="javascript:show_modal(\''.route("admin.equipment.edit", $data_list[$i]['id']).'\');" class="edit-btn blue modal-trigger" title="Edit"><i class="icon-note"></i></a>&nbsp;
                <a href="'.route('admin.equipment.delete', $data_list[$i]['id']).'" onclick="return confirm(\'Are you sure to delete?\')" class="delete-btn red" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
               </span>' : '<span class="span-gender">
                <a ahref="'.route("admin.equipment.move", [$data_list[$i-1]['id'], $data_list[$i]['id']]).'" class="edit-btn blue a-move-row" onclick="doOnMoveRow(this)" title="Move Up"><i class="glyphicon glyphicon-arrow-up"></i></a>&nbsp;
                <a href="javascript:show_modal(\''.route("admin.equipment.edit", $data_list[$i]['id']).'\');" class="edit-btn blue modal-trigger" title="Edit"><i class="icon-note"></i></a>&nbsp;
                <a href="'.route('admin.equipment.delete', $data_list[$i]['id']).'" onclick="return confirm(\'Are you sure to delete?\')" class="delete-btn red" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
               </span>')
               :
               ($i==0 ? '<span class="span-gender">
               <a ahref="'.route("admin.equipment.move", [( $i == $end - 1 ? $data_list[$i]['id'] : $data_list[$i+1]['id'] ), $data_list[$i]['id']]).'" class="edit-btn blue a-move-row" onclick="doOnMoveRow(this)" title="Move Down"><i class="glyphicon glyphicon-arrow-down"></i></a>&nbsp;
               <a href="javascript:show_modal(\''.route("admin.equipment.edit", $data_list[$i]['id']).'\');" class="edit-btn blue" title="Edit"><i class="icon-note"></i></a>&nbsp;
               <a href="'.route('admin.equipment.delete', $data_list[$i]['id']).'" onclick="return confirm(\'Are you sure to delete?\')" class="delete-btn red" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
              </span>' :
              '<span class="span-gender">
                <a ahref="'.route("admin.equipment.move", [$data_list[$i-1]['id'], $data_list[$i]['id']]).'" class="edit-btn blue a-move-row" onclick="doOnMoveRow(this)" title="Move Up"><i class="glyphicon glyphicon-arrow-up"></i></a>&nbsp;
                <a ahref="'.route("admin.equipment.move", [$data_list[$i+1]['id'], $data_list[$i]['id']]).'" class="edit-btn blue a-move-row" onclick="doOnMoveRow(this)" title="Move Down"><i class="glyphicon glyphicon-arrow-down"></i></a>&nbsp;
                <a href="javascript:show_modal(\''.route("admin.equipment.edit", $data_list[$i]['id']).'\');" class="edit-btn blue modal-trigger" title="Edit"><i class="icon-note"></i></a>&nbsp;
                <a href="'.route('admin.equipment.delete', $data_list[$i]['id']).'" onclick="return confirm(\'Are you sure to delete?\')" class="delete-btn red" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
               </span>')
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
        $transaction_id = $request->get('transaction_id');
        $user_id = $request->get('user_id');
        $equipment_name = $request->get('equipment_name');
        $form_type = $request->get('form_type');
        $status = $request->get('status');
        if ( $transaction_id == 'NULL' ) {
            $record = new EquipmentTable();
            $last_sequence = app(EquipmentTable::class)->max('sequence');
            $record->sequence = $last_sequence+1;
            $record->created_at = time();
        }
        else {
            $record = app(EquipmentTable::class)->where('id', $transaction_id)->first();
        }
        

        if ( $request->file('photo') ) {
            $dest_path = base_path('/uploads/uploads_type/');

            if ( !file_exists($dest_path) ) {
                \File::makeDirectory($dest_path, 0777, true);
            }

            if ( $record->equipment_image != '' || is_null($record->equipment_image) ) {
                $dest_path_ = base_path('/');
                if ( file_exists($dest_path_ . $record->equipment_image) ) {
                    \File::delete($dest_path_ . $record->equipment_image);
                }
            }
            $imageName = 'photo__'.$record->id.'_'.time().'.'.$request->file('photo')->getClientOriginalExtension();
            $record->equipment_image = 'uploads/uploads_type/'.$imageName;
            
            $manager = new ImageManager();
            $image = $manager->make($request->file('photo'))->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($dest_path.$imageName);
        }
        $record->equipment_name = $equipment_name;
        $record->form_type = $form_type;
        $record->status = $status == 'active' ? 1 : 0;

        $record->save();

        return redirect()->route('admin.equipment.list');
    }
    public function changeOrder($prevId, $curId) {
        $prevRecord = app(EquipmentTable::class)->where('id', $prevId)->first();
        $curRecord = app(EquipmentTable::class)->where('id', $curId)->first();
        $temp = $curRecord->sequence;
        $curRecord->sequence = $prevRecord->sequence;
        $prevRecord->sequence = $temp;
        $curRecord->save();
        $prevRecord->save();
        return redirect()->route('admin.equipment.list');
    }
    public function deleteData($id) {
        $record = app(EquipmentTable::class)->where('id', $id)->first();
        $dest_path = base_path('/');
        if ( $record->equipment_image != '' || is_null($record->equipment_image) ) {
            if ( file_exists($dest_path) ) {
                \File::delete($dest_path . $record->equipment_image);
            }
        }
        if ($record) $record->delete();
        return redirect()->route('admin.equipment.list');
    }
    public function downloadCSV() {
        $sql = "SELECT * FROM tbl_equipment WHERE 1=1";
        $rows = DB::select($sql);
        $data_list = array();
        
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$data_record = array();
                foreach( $row as $key=>$val ) {
                    if ($key == 'act') $data_record[$key] = $statues[$val];
                    else if ($key == 'date_created' || $key == 'member_since' || $key == 'created_at') $data_record[$key] = date('Y-m-d H:i:s', $val);
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
            return redirect()->route('admin.equipment.list');
        }
    }
    public function frmSampleImages($equipment_id) {
        $data['equipment_id'] = $equipment_id;
        return view('pages.equipment.sample_images', $data);
    }
    public function fetchSampleImageList($equipment_id) {
        if ($equipment_id != 17) {
            $sampleImageRecords = app(EquipmentSampleImagesTable::class)->where('equipment_id', $equipment_id)->orderBy('orderNum', 'ASC')->get();
        }
        else {
            $sampleImageRecords = app(EquipmentSampleImagesTable::class)->where('equipment_id', $equipment_id)->where('subtype_id', 0)->orderBy('orderNum', 'ASC')->get();
        }
        $data['images'] = [];
        if (count($sampleImageRecords) > 0) {
            foreach($sampleImageRecords as $sortNum=>$record) {
                $image = array();
                $image['id'] = $record->id;
                $image['sample_name'] = $record->sample_name;
                $image['sample_url'] = Common::getBackendImg($record->sample_url);
                $image['orderNum'] = $record->orderNum;
                $image['sortNum'] = $sortNum+1;
                $image['date_created'] = date('Y-m-d H:i:s', $record->date_created);
                $data['images'][] = $image;
            }
        }
        return response()->json($data);
    }
    public function postUploadSampleImages(Request $request) {
        ini_set('max_execution_time', '0');
        $equipment_id = $request->get('equipment_id');
        if($request->TotalFiles > 0) {
                
           for ($x = 0; $x < $request->TotalFiles; $x++) {
                $record = new EquipmentSampleImagesTable();
                $path_str = 'uploads/uploads_sample/';
                $dest_path = base_path($path_str);

                if ( !file_exists($dest_path) ) {
                    \File::makeDirectory($dest_path, 0777, true);
                }

                if ($request->hasFile('files'.$x)) {
                    $file      = $request->file('files'.$x);
                    $imageName = 'photo__'.$equipment_id.'_'.$x.'_'.time().'.'.$file->getClientOriginalExtension();

                    $width = \Image::make($file)->width();
                    $height = \Image::make($file)->height();
                    if ( $width > 600 ) {
                        $_ratio = 600 / $width;
                        $manager = new ImageManager();
                        $manager->make($file)->resize(600, $height*600/$width)->save($dest_path.$imageName);
                        // $manager->make($file)->resize(800, null, function ($constraint) {
                        //     $constraint->aspectRatio();
                        // })->save($dest_path.$imageName);
                    }
                    else {
                        $file->move($dest_path, $imageName);
                    }


                    $record->equipment_id = $equipment_id;
                    $record->sample_url = $path_str.$imageName;
                    $record->date_created = time();
                    $lastOrderNum = app(EquipmentSampleImagesTable::class)->where('equipment_id', $equipment_id)->max('orderNum');
                    $record->orderNum = $lastOrderNum+1;
                    $record->save();
                }
           }
 
            // File::insert($insert);
 
            // return response()->json(['success'=>'Ajax Multiple fIle has been uploaded']);
 
          
        }
        // if ($request->hasfile('file_sample_images') && count($request->file('file_sample_images')) > 0) {
        //     foreach($request->file('file_sample_images') as $idx=>$file) {
        //         $record = new EquipmentSampleImagesTable();
        //         $path_str = 'uploads/uploads_sample/';
        //         $dest_path = base_path($path_str);

        //         // if ( !file_exists($dest_path) ) {
        //         //     \File::makeDirectory($dest_path, 0777, true);
        //         // }

        //         $imageName = 'photo__'.$equipment_id.'_'.$idx.'_'.time().'.'.$file->getClientOriginalExtension();
                
        //         $width = \Image::make($file)->width();
        //         $height = \Image::make($file)->height();
                
        //         if ( $width > 800 ) {
        //             $_ratio = 800 / $width;
        //             $manager = new ImageManager();
        //             $manager->make($file)->resize(800, $height*800/$width)->save($dest_path.$imageName);
        //             // $manager->make($file)->resize(800, null, function ($constraint) {
        //             //     $constraint->aspectRatio();
        //             // })->save($dest_path.$imageName);
        //         }
        //         else {
        //             $file->move($dest_path, $imageName);
        //         }
                
        //         $record->equipment_id = $equipment_id;
        //         $record->sample_url = $path_str.$imageName;
        //         $record->date_created = time();
        //         $lastOrderNum = app(EquipmentSampleImagesTable::class)->where('equipment_id', $equipment_id)->max('orderNum');
        //         $record->orderNum = $lastOrderNum+1;
        //         $record->save();
        //     }
        // }
        // return redirect()->route('admin.equipment.sample_images', $equipment_id);
        return response()->json('ok');
    }
    public function changeImageTitle($id, $title) {
        $row = app(EquipmentSampleImagesTable::class)->where('id', $id)->first();
        $row->sample_name = $title;
        $row->save();
        return response()->json('ok');
    }
    public function removeSampleImage($id) {
        $row = app(EquipmentSampleImagesTable::class)->where('id', $id)->first();
        $dest_path = base_path('/');
        if ( $row->sample_url != '' || is_null($row->sample_url) ) {
            if ( file_exists($dest_path) ) {
                \File::delete($dest_path . $row->sample_url);
            }
        }
        $row->delete();
        return response()->json('ok');
    }
    public function postSortSampleImages(Request $request) {
        $ids = $request->get('ids');
        $ids = explode(',', $ids);
        if (count($ids) > 0) {
            foreach($ids as $index=>$id) {
                $record = app(EquipmentSampleImagesTable::class)->where('id', $id)->first();
                // $record->orderNum = count($ids) - $index;
                $record->orderNum = $index+1;
                $record->save();
            }
        }
        return response()->json('ok');
    }
}
