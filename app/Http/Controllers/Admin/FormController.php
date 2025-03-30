<?php

namespace App\Http\Controllers\Admin;

use App\Common;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FormTable;
use App\Models\EquipmentTable;
use App\Models\EquipmentSampleImagesTable;
use App\Models\EquipmentPhotoTable;
use App\Models\AdminAuth;
use App\Models\StoreTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Intervention\Image\ImageManager;
use App\Exports\ExcelExport;
use App\Imports\ExcelImport;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class FormController extends Controller
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
        $data['filter_from'] = date('Y-m-d', strtotime("-1 days"));
        $data['filter_to'] = date('Y-m-d');
        return view('pages.forms.list', $data);
    }
    public function viewDetail($formId) {
        $data = array();
        if ( strtolower($formId) != 'null' ) {
            $sql = "SELECT u.name agent_name, e.equipment_name, e.form_type, e.status, f.*
                FROM tbl_form f
                JOIN users u ON u.id = f.agent_id
                JOIN tbl_equipment e ON e.id = f.equipment_id
                WHERE f.id=".$formId;
            $record = \DB::selectOne($sql);
            foreach($record as $key=>$value) {
                if ( $key == 'date_created' || $key == 'date_updated' ) {
                    $data[$key] = date('Y-m-d H:i:s', $value);
                }
                else if ( $key == "value_json" ) {
                    if ($value == '') {
                        $data[$key] = [];
                    }
                    else {
                        if ($record->form_type != 19 && $record->form_type != 20) {
                            $data[$key] = json_decode($value, true);
                        }
                        else {
                            $temps = json_decode($value, true);
                            $temp = [];
                            foreach($temps as $val) {
                                foreach($val as $k=>$v) {
                                    $temp[$k] = $v;
                                }
                            }
                            $data['value_json'] = $temp;
                        }
                    } 
                }
                else {
                    $data[$key] = $value;
                }
            }
        }
        // dd($data);
        return view('pages.forms.view', $data);
    }
    
    public function getListData() {
        $timezone = request()->get('timezone');
        $search = request()->get('search');
        $data_list = array();

        $sql = "SELECT u.name agent_name, e.equipment_name, f.*
                FROM tbl_form f
                JOIN users u ON u.id = f.agent_id
                JOIN tbl_equipment e ON e.id = f.equipment_id
                WHERE 1=1 ";
        if ( !is_null($search['value']) ) {
            $search_value = $search['value'];
            $sql .= " AND ( f.contact_name LIKE '%{$search_value}%' OR f.year LIKE '%{$search_value}%' OR f.model LIKE '%{$search_value}%' OR f.make LIKE '%{$search_value}%' OR u.name LIKE '%{$search_value}%' OR e.equipment_name LIKE '%{$search_value}%' )";
        }
        $order_str = " ORDER BY f.date_created DESC";

        $sql .= $order_str;
        $rows = DB::select($sql);
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$data_record = array();
				$data_record['id'] = $row->id;
				$data_record['date_created'] = date('Y-m-d H:i:s', $row->date_created);
				// $data_record['date_created'] = Common::convertUTCtoLocal(date('Y-m-d H:i:s', $row->date_created), $timezone, 'Y-m-d H:i:s');
				$data_record['agent_name'] = $row->agent_name;
				$data_record['equipment_name'] = $row->equipment_name;
				$data_record['year'] = $row->year;
				$data_record['make'] = $row->make;
				$data_record['model'] = $row->model;
				$data_record['contact_name'] = $row->contact_name;

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
              $data_list[$i]['date_created'],
              $data_list[$i]['agent_name'],
              $data_list[$i]['equipment_name'],
              $data_list[$i]['year'] . ' | ' . $data_list[$i]['make'] . ' | ' . $data_list[$i]['model'],
              $data_list[$i]['contact_name'],
              '<span class="span-gender">
                <a href="javascript:show_modal(\''.route("admin.forms.view", $data_list[$i]['id']).'\');" class="edit-btn blue modal-trigger" title="View Detail"><i class="fa fa-eye"></i></a>&nbsp;
                <a href="javascript:show_modal(\''.route('admin.forms.view_images', $data_list[$i]['id']).'\');" class="edit-btn rd-purple modal-trigger" title="View Equipment Images"><i class="fa fa-file-picture-o"></i></a>&nbsp;
                <a onclick="doOnClickDownloadTag(this, event)" href="'.route('admin.forms.download_excel', $data_list[$i]['id']).'" class="edit-btn green" title="Download Excel"><i class="fa fa-file-excel-o"></i></a>&nbsp;
                <a onclick="doOnClickDownloadTag(this, event)" href="'.route('admin.forms.download_zip', $data_list[$i]['id']).'" class="edit-btn red" title="Download Zip"><i class="fa fa-file-zip-o"></i></a>
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

    public function viewEquipImages($form_id) {
        $data = ['form_id'=>$form_id];
        return view('pages.forms.view_images', $data);
    }
    public function fetchEquipImages($formId) {
        $formRecord = app(FormTable::class)->where('id', $formId)->first();
        $sql = "SELECT ep.*, IF(esi.sample_name IS NULL, 'Additional Photo', esi.sample_name) sample_name, esi.sample_url, esi.orderNum
                FROM tbl_equip_photos ep
                LEFT JOIN tbl_equipment_sample_images esi
                ON esi.id = ep.sample_id
                WHERE ep.equipment_id = '{$formRecord->equipment_id}' AND ep.form_id = '{$formId}' ORDER BY esi.orderNum ASC";
        
        $records = \DB::select($sql);
        $data = ['images'=>[]];
        if (count($records) > 0) {
            foreach( $records as $record ) {
                $image = [];
                foreach($record as $key=>$value) {
                    if ($key == 'date_created') {
                        $image['date_created'] = date('Y-m-d H:i:s', $value);
                    }
                    else if ($key == 'photo_url' || $key == 'sample_url') {
                        $image[$key] = Common::getBackendImg($value);
                    }
                    else {
                        $image[$key] = $value;
                    }
                }
                $data['images'][] = $image;
            }
        }
        return response()->json($data);
    }
    public function postChangeEquipImage(Request $request) {
        $photo_id = $request->get('photo_id');
        if ( $request->file('photo') ) {
            $record = app(EquipmentPhotoTable::class)->where('id', $photo_id)->first();

            $dest_path = base_path('/uploads/equip/');

            if ( !file_exists($dest_path) ) {
                \File::makeDirectory($dest_path, 0777, true);
            }

            if ( $record->photo_url != '' || is_null($record->photo_url) ) {
                $dest_path_ = base_path('/');
                if ( file_exists($dest_path_ . $record->photo_url) ) {
                    \File::delete($dest_path_ . $record->photo_url);
                }
            }
            $converted_sample_id = $record->sample_id == '-1' ? 999 : $record->sample_id;
            $imageName = $record->agent_id.'_'.$record->equipment_id.'_'.$converted_sample_id.'_'.time().'.'.$request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move($dest_path, $imageName);
            $record->photo_url = 'uploads/equip/'.$imageName;
            $record->Save();
        }
        return response()->json('ok');
    }
    private function getExcelData($formId, $timezone) {
        $sql = "SELECT u.name agent_name, e.equipment_name, e.form_type, f.*
                FROM tbl_form f
                JOIN users u
                ON f.agent_id=u.id 
                JOIN tbl_equipment e
                ON e.id = f.equipment_id WHERE f.id='{$formId}'";
        $row = \DB::selectOne($sql);
        if (is_null($row)) return redirect()->route('admin.forms.list');
        $fieldData = json_decode(json_encode($row), true);
        $fieldData['date_created1'] = date('d-M-y', $fieldData['date_created']);
        // dd($fieldData);
        $fieldData['value_json'] = $fieldData['value_json'] == '' ? [] : json_decode($fieldData['value_json'], true);
        $fieldData['json_height'] = count($fieldData['value_json']) == 0 ? 16 : count($fieldData['value_json'])*16;
        // dd($fieldData['value_json']);
        $fieldData['additional_info_height'] = $fieldData['additional_info'] == '' ? 16 :  ceil(strlen($fieldData['additional_info'])/64)*16;
        $fieldData['additional_info'] = htmlentities($fieldData['additional_info']);
        $fieldData['thumb_html'] = '';
        $a = [];
        $temp = [];
        if ($fieldData['form_type'] == 19 || $fieldData['form_type'] == 20) {
            $temps = $fieldData['value_json'];
            foreach($temps as $val) {
                foreach($val as $k=>$v) {
                    $temp[$k] = $v;
                }
            }
            $fieldData['value_json'] = $temp;
        }
        // dd($fieldData['value_json']);
        if (count($fieldData['value_json']) > 0) {
            foreach($fieldData['value_json'] as $key=>$val) {
                $a[] = "&#8226; &nbsp;$key :&nbsp; $val";
            }
        }
        $fieldData['thumb_html'] = implode("<br/>", $a);
        // dd($fieldData);
        // $fieldData['date_created1'] = Common::convertUTCtoLocal(date('Y-m-d H:i:s', $fieldData['date_created']), $timezone, 'd-M-y');
        // dd($fieldData['date_created'], date('Y-m-d H:i:s', $fieldData['date_created']), $timezone, $fieldData['date_created1'], Common::convertUTCtoLocal(date('Y-m-d H:i:s', $fieldData['date_created']), $timezone, 'Y-m-d H:i:s'));
        return $fieldData;
    }
    public function downloadExcel($formId) {
        $timezone = request()->get('timezone');
        $fieldData = $this->getExcelData($formId, $timezone);
        $filename = $fieldData['year'].' '.$fieldData['make'].' '.$fieldData['model'].' '.$fieldData['equipment_name'].'.xlsx';
        $converted_equip_name = str_replace(array("\\", "/"), "-", $filename);
        // dd($converted_equip_name, $fieldData);
        return Excel::download(new ExcelExport($fieldData), $converted_equip_name);
    }
    public function downloadZip($formId) {
        $timezone = request()->get('timezone');
        $excelData = $this->getExcelData($formId, $timezone);
        $excel_filename = $excelData['year'].' '.$excelData['make'].' '.$excelData['model'].' '.$excelData['equipment_name'].'.xlsx';
        $excel_filename = str_replace(array("\\", "/"), "-", $excel_filename);
        $excel_filename = trim(preg_replace('/\s\s+/', ' ', $excel_filename));

        $destinationPath = base_path('uploads/temp_excels/');
        $excel_filePath = $destinationPath.$excel_filename;
        Excel::store(new ExcelExport($excelData), $excel_filename, 'public');

        $sql = "select ep.*, if(esi.sample_name is null, 'Advanced Photo', esi.sample_name) sample_name
        from tbl_equip_photos ep	
        left join tbl_equipment_sample_images esi
        on ep.sample_id = esi.id
        where ep.form_id='{$formId}';";
        $row = \DB::select($sql);
        
        $formData = [];
        if (!is_null($row)) $formData = json_decode(json_encode($row), true);
        
        $zip = new  ZipArchive();
        $zip_filename = $formId." ".$excelData['year']." ".$excelData['make']." ".$excelData['model']." ".$excelData['equipment_name']." - ".$excelData['contact_name'];
        $zip_filename = str_replace(array("\\", "/"), "-", $zip_filename);
        $zip_filename = trim(preg_replace('/\s\s+/', ' ', $zip_filename));

        $zipFile = $zip_filename.".zip";
        $zipStatus = $zip->open(storage_path($zipFile), ZipArchive::CREATE);
        $dd = array();
        if ($zipStatus == true) {
            if (count($formData) > 0) {
                foreach($formData as $imageDetail){
                    $dest_path_ = base_path('/');
                    $path2 = $dest_path_ . $imageDetail['photo_url'];
                    if(File::exists($path2)){
                        $zip->addFile($path2, basename($path2));
                    }
                }
            }
            if (File::exists($excel_filePath)) {
                $zip->addFile($excel_filePath, basename($excel_filePath));
                $dd[] = $excel_filePath;
            }
            $zip->close();
            File::delete($excel_filePath);
        }
        return response()->download(storage_path($zipFile))->deleteFileAfterSend(true);
    }
    public function frmCsvDownload() {
        $data['filter_from'] = date('Y-m-d', strtotime("-1 days"));
        $data['filter_to'] = date('Y-m-d');
        return view('pages.forms.csv_download', $data);
    }
    public function downloadCSV($from, $to) {
        $from_timestamp = strtotime($from);
        $to_timestamp = strtotime($to." 23:00:00");

        $sql = "SELECT u.name agent_name, e.equipment_name, f.*
                FROM tbl_form f
                JOIN users u ON u.id = f.agent_id
                JOIN tbl_equipment e ON e.id = f.equipment_id 
                WHERE f.date_created >={$from_timestamp} AND f.date_created <= {$to_timestamp} ORDER BY f.date_created DESC";
// echo($sql);exit;
        $rows = DB::select($sql);
        $data_list = array();
        
		if (count($rows) > 0) {
			foreach($rows as $row) {
				$data_record = array();
                $data_record['type'] = $row->equipment_name;
                $data_record['year'] = $row->year;
                $data_record['make'] = $row->make;
                $data_record['model'] = $row->model;
                // $data_record['odometer'] = '';
                $data_record['SN/VIN'] = $row->serial;
                $value_json = json_decode($row->value_json, true);

                $engine = '';
                $arr = [];
                if (!is_null($value_json)) {
                    foreach($value_json as $key => $val) $arr[strtolower($key)] = $val;
                }
                $data_record['engine'] = is_null($value_json) ? '' : (array_key_exists('engine', $arr) ? $arr['engine'] : '');
                $data_record['EPA Label Present'] = $row->epa_label;
                $data_record['agent name'] = $row->agent_name;
                $data_record['customer name'] = $row->contact_name;
                // foreach( $row as $key=>$val ) {
                //     if ($key == 'act') $data_record[$key] = $statues[$val];
                //     else if ($key == 'date_created' || $key == 'member_since' || $key == 'created_at') $data_record[$key] = date('Y-m-d H:i:s', $val);
                //     else $data_record[$key] = $val;
                // }
				$data_list[] = $data_record;
			}
		}
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=form_{$from}-{$to}_csv.csv",
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
            return redirect()->route('admin.forms.list');
        }
        return redirect()->route('admin.forms.list');
    }

    public function importExcel(Request $request) {
        Excel::import(new ExcelImport, $request->file('excel_file'));
    }
    
}
