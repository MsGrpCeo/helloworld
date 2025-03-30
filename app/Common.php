<?php

namespace App;

use App\Models\NotificationTable;
use App\Models\AdminAuth;
use App\Models\StoreTable;
use App\Models\UserTable;
use App\Models\TransactionTable;
use App\Models\IngredientTable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;

class Common
{
    public static function getServerLink() {
        $isHttps = 'http';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $isHttps = 'https';
        }
        if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $isHttps = 'https';
        }        

        $serverLink = $isHttps.'://'.$_SERVER['HTTP_HOST'];
        return $serverLink;
    }
    public static function convertTimeToLocal($localtime, $local_timezone, $format='Y-m-d H:i:s') {
        $dt = new \DateTime($localtime);
        $dt->setTimeZone(new \DateTimeZone($local_timezone));
        
        return $dt->format($format);
    }
    public static function convertLocaltoUTC($localtime, $local_timezone, $format) {
        $tz_to = 'UTC';

        $dt = new \DateTime($localtime, new \DateTimeZone($local_timezone));
        $dt->setTimeZone(new \DateTimeZone($tz_to));
        
        return $dt->format($format);
    }
    public static function convertUTCtoLocal($utc_time, $local_timezone, $format='Y-m-d H:i:s') {
        $tz_to = 'UTC';

        $dt = new \DateTime($utc_time, new \DateTimeZone($tz_to));
        $dt->setTimeZone(new \DateTimeZone($local_timezone));
        
        return $dt->format($format);
    }
    public static function getAllUserList( $store_owner ) {
        $model = app(UserTable::class);
        if ( $store_owner->utype != 1 ) {
            $model->where('company_id', $store_owner->id);
        }
    }
    public static function getAllStoreList() {
        $records = app(StoreTable::class)->where('utype', '<>', 1)->get();
        $ret = array();
        $ret[] = array('id'=>'all', 'name'=>'All');
        if ( $records ) {
            foreach( $records as $record ) {
                $ret[] = array('id'=>$record->id, 'name'=>$record->company_name, 'act'=>$record->act);
            }
        }
        return $ret;
    }
    public static function getAllStoreListEx() {
        $records = app(StoreTable::class)->where('utype', '<>', 1)->get();
        $ret = array();
        if ( $records ) {
            foreach( $records as $record ) {
                $ret[] = array('id'=>$record->id, 'name'=>$record->company_name, 'act'=>$record->act);
            }
        }
        return $ret;
    }
    public static function getGlobalUserList() {
        $ret = array();
        $records = app(UserTable::class)->where('utype', 2)->where('push_enabled', 1)->get();
        if ( $records ) $ret = $records->toArray();
        return $ret;
    }
    public static function getGlobalUserListEx() {
        $ret = array();
        $records = app(UserTable::class)->where('utype', 2)->where('push_enabled', 1)->get();
        if ( $records ) $ret = $records;
        return $ret;
    }
    public static function getAnyUserList($store_id) {
        $sql = "SELECT u.*, c.company_name 
                FROM tbl_user u
                JOIN tbl_fav_store fs
                ON fs.user_id = u.id
                JOIN tbl_company c
                ON c.id = fs.store_id
                WHERE u.utype = 2 AND fs.store_id = $store_id AND u.push_enabled=1";
        $ret = array();
        $rows = DB::select($sql);
        if ( $rows ) $ret = $rows;
        return $ret;
    }
    public static function getGlobalAgentList($store_id = null) {
        $ret = array();

        if ( !is_null($store_id) ) {
            $records = app(UserTable::class)->where('utype', 3)->where('company_id', $store_id)->get();
        }
        else {
            $records = app(UserTable::class)->where('utype', 3)->get();
        }
        if ( $records ) $ret = $records->toArray();
        return $ret;
    }

    public static function getNotificationList( $user ) {
        if ( $user->utype == 1 ) {
            $store_id = null;
        }
        else {
            $store_id = $user->id;
        }
        $ret = array('sent'=>array(), 'scheduled'=>array());
        if ( is_null($store_id) ) {
            $records = app(NotificationTable::class)->get();
        }
        else {
            $records = app(NotificationTable::class)->where('store_id', $store_id)->get();
        }
        if ( $records ) {
            foreach( $records as $record ) {
                if ( $record['status'] == 1 ) {
                    $ret['scheduled'][] = $record;
                }
                else {
                    $ret['sent'][] = $record;
                }
            }
        }
        return $ret;
    }
    public static function divArray( $user_arr ) {
        $ret = array('active'=>array(), 'inactive'=>array());
        if ( $user_arr ) {
            foreach( $user_arr as $record ) {
                $record = self::stdToArray(($record));
                if ( $record['act'] == 1 ) {
                    $ret['active'][] = $record;
                }
                else {
                    $ret['inactive'][] = $record;
                }
            }
        }
        return $ret;
    }
    
    public static function stdToArray( $stdObject ) {
        $ret = array();
        foreach($stdObject as $k=>$v) {
            $ret[$k] = $v;
        }
        return $ret;
    }

    public static function getStateListfromUser() {
        $ret = array('all'=>'All', 'not_entered'=>'Not Entered');
        $records = app(UserTable::class)->where('utype', 2)->whereNotNull('state')->where('state', '<>', '')->groupBy('state')->get();
        if ( $records ) {
            foreach( $records as $record ) {
                $ret[$record->state] = $record->state;
            }
        }
        return $ret;
    }

    /*****************************************   ONESIGNAL   ******************************************  */
    public static function sendPushMessage($message, $player_id_arr, $type, $data, $notification, $img_link){
		$content = [ "en" => $message ];
		
		$fields = array(
			'app_id' => Config::get('app.onsignal_app_id'),
			'include_player_ids' => $player_id_arr,
			'data' => array("type" => $type,"detail_id" => $data),
			'contents' => $content,
			'ios_badgeType' => "SetTo",
			'ios_badgeCount' => "1",
            'ios_sound' => $notification,
            'ios_attachments' => ['id1'=>$img_link],
            'chrome_big_picture' => $img_link,
            'adm_big_picture' => $img_link,
            'chrome_web_image' => $img_link,
            'big_picture' => $img_link,
            // 'ios_attachments' => $img_link,
		);
		
		$fields = json_encode($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic '.Config::get('app.onsignal_token')));
//		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
    public static function sendPushMessageToAll($message,$type) {
        $content = [ "en" => $message ];
        $fields = array(
            'app_id' => Config::get('app.onsignal_app_id'),
            'included_segments' => array( 'All' ),
            'data' => array( "type" => $type ),
            'contents' => $content,
            'ios_badgeType' => "SetTo",
            'ios_badgeCount' => "1"
        );
        
        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.Config::get('app.onsignal_token')
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    public static function sendPushMessageEx($message, $player_id_arr, $type, $data){
		$content = [ "en" => $message ];
		
		$fields = array(
			'app_id' => Config::get('app.onsignal_app_id'),
			'include_player_ids' => $player_id_arr,
			'data' => array("type" => $type,"detail_id" => $data),
			'contents' => $content,
			'ios_badgeType' => "SetTo",
			'ios_badgeCount' => "1",
            // 'ios_sound' => $notification,
            // 'ios_attachments' => $img_link,
		);
		
		$fields = json_encode($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic '.Config::get('app.onsignal_token')));
//		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
        // dd($response);
		curl_close($ch);
		
		return $response;
	}

    public static function getCoordinatesFromZip($zip) {
        $url = "https://api.promaptools.com/service/us/zip-lat-lng/get/?zip={$zip}&key=17o8dysaCDrgv1c";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        if ( $response->status == 1 ) {
            return ['status'=>200, 'zip'=>$zip, 'latitude'=>$response->output[0]->latitude, 'longitude'=>$response->output[0]->longitude];
        }
        else {
            return null;
        }
        
    }
    //SELECT * FROM tbl_user WHERE utype=2 AND lat=0 AND lon=0 AND LENGTH(zip) =5 AND zip REGEXP '^-?[0-9]+$' GROUP BY zip

    public static function makeFieldToCsvHeader($fieldStr) {
        $tmp = str_replace('_', ' ', $fieldStr);
        return ucfirst($tmp);
    }
    
    public static function getBackendImg($img_link=null) {
        $dest_path = base_path('/');
        $backend_img = url('/public/images/image_placeholder.png');
        if ( $img_link != '' && !is_null($img_link) ) {
            if ( file_exists($dest_path.$img_link) ) {
                $backend_img = self::getServerLink() . '/' . $img_link;
            }
        }
        return $backend_img;
    }

    public static function getCategoryList() {
        $tags = ['Breakfast', 'Lunch', 'Dinner', 'Sides', 'Dessert', 'Starters'];
        return $tags;
    }
    public static function getDietaryList() {
        $tags = ['Vegan', 'Vegetarian', 'Pescatarian', 'Pork-free', 'Halal', 'Egg-free', 'Paleo', 'Low-carb', 'Low-sugar', 'Keto', 'Gluten-free'];
        return $tags;
    }
    public static function getAllergenList() {
        $tags = ['Barley', 'Celery', 'Corn', 'Dairy', 'Eggs', 'Fish', 'Lupin', 'Milk', 'Mustard', 'Oats', 'Peanuts', 'Rye', 'Sesame', 'Shellfish', 'Soy', 'Tree Nut', 'Wheat'];
        return $tags;
    }
    public static function getCuisineList() {
        $tags = ['Italian', 'Japanese', 'Indian', 'Mexican', 'American', 'Greek', 'French', 'Thai', 'Spanish', 'Chinese', 'Korean', 'German', 'Mediterranean', 'Other'];
        return $tags;
    }
    public static function getIngredientsList() {
        $ingredients = app(IngredientTable::class)->orderBy('name', 'ASC')->get();
        $tags = [];
        if ( count($ingredients) > 0 ) {
            foreach( $ingredients as $ingredient ) {
                // $tags[] = array('id'=>$ingredient->id, 'value'=>$ingredient->name);
                $tags[] = $ingredient->name;
            }
        }
        return $tags;
    }
    public static function getNextPayDate($row) {
        if ( strtolower($row->pay_frequency) == "bi-monthly" ) {
            $next_date1 = date('Y-m-d', strtotime('+1 month', strtotime($row->last_pay_date1)));
            while ($next_date1 < date('Y-m-d')) {
                $next_date1 = date('Y-m-d', strtotime('+1 month', strtotime($next_date1)));
            }
            $next_date2 = date('Y-m-d', strtotime('+1 month', strtotime($row->last_pay_date2)));
            while ($next_date2 < date('Y-m-d')) {
                $next_date2 = date('Y-m-d', strtotime('+1 month', strtotime($next_date2)));
            }
            $ret_date = $next_date1<$next_date2 ? $next_date1 : $next_date2;
            return date('d-m-Y', strtotime($ret_date));
        }
        else {
            $stepTime = '+1 month';
            if ( strtolower($row->pay_frequency) == "monthly" ) $stepTime = '+1 month';
            if ( strtolower($row->pay_frequency) == "weekly" ) $stepTime = '+1 week';
            if ( strtolower($row->pay_frequency) == "fortnightly" ) $stepTime = '+2 week';
            $arr = [];
            $next_date = date('Y-m-d', strtotime($stepTime, strtotime($row->last_pay_date1)));
            $arr[] = $next_date;
            while ($next_date < date('Y-m-d')) {
                $next_date = date('Y-m-d', strtotime($stepTime, strtotime($next_date)));
                $arr[] = $next_date;
            }
            return date('d-m-Y', strtotime($next_date));
        }
        
        return date("d-m-Y");
    }
    public static function get_pay_amount($user_id, $type, $till = null) {
        $retAmount = 0;
        $userRecord = app(UserTable::class)->where('id', $user_id)->first();
        if ( $type == 0 ) {
            $retAmount = $userRecord->amount_to_be_paid_daily;
        }
        else {
            $nextPayDate = self::getNextPayDate($userRecord);
            if ( strtolower($userRecord->pay_frequency) == "bi-monthly" ) {
                $next_date1 = date('Y-m-d', strtotime('+1 month', strtotime($userRecord->last_pay_date1)));
                while ($next_date1 <= date('Y-m-d')) {
                    $next_date1 = date('Y-m-d', strtotime('+1 month', strtotime($next_date1)));
                }
                $next_date2 = date('Y-m-d', strtotime('+1 month', strtotime($userRecord->last_pay_date2)));
                while ($next_date2 <= date('Y-m-d')) {
                    $next_date2 = date('Y-m-d', strtotime('+1 month', strtotime($next_date2)));
                }
                $ret_date = $next_date1<$next_date2 ? $next_date1 : $next_date2;

                $pay_timestamp = strtotime('-1 month', strtotime($ret_date));
            }
            else {
                $stepTime = '-1 month';
                if ( strtolower($userRecord->pay_frequency) == "monthly" ) $stepTime = '-1 month';
                if ( strtolower($userRecord->pay_frequency) == "weekly" ) $stepTime = '-1 week';
                if ( strtolower($userRecord->pay_frequency) == "fortnightly" ) $stepTime = '-2 week';
                $pay_timestamp = strtotime($stepTime, strtotime($nextPayDate));
            }
            $time = $userRecord->service_start_date == '' ? $pay_timestamp : ($pay_timestamp >= $userRecord->service_start_date ? $pay_timestamp : $userRecord->service_start_date);
            $dailyFee = $userRecord->my_daily_fee_percent;
            $to = time();
            if ( !is_null($till) ) {
                $to = $till;
            }
            $transactions = app(TransactionTable::class)
                                ->where('user_id', $userRecord->id)
                                ->where("transaction_date", ">=", $time)
                                ->where('transaction_date', '<=', $to)
                                ->whereIn('status', array(1, 5))
                                ->get();
            $sum_should_refund = 0;
            // if ($user_id == 424) dd($nextPayDate, $time, $to, $transactions->toArray());
            if ( count($transactions) > 0 ) {
                $transactions = $transactions->toArray();
                foreach( $transactions as $transaction ) {
                    if ( $transaction['type'] == 0 ) {
                        $tempVal = $dailyFee/100;
                        $tempVal = 1 - $tempVal;
                        if ( $tempVal != 0 ) {
                            $sum_should_refund += $transaction['amount'] / $tempVal;
                        }
                    }
                    else {
                        $sum_should_refund -= $transaction['amount'];
                    }
                }
            }
            $retAmount = $sum_should_refund;
        }
        return $retAmount;
    }
    public static function get_pay_amount_in_user($user_id, $type, $till = null) {
        $retAmount = 0;
        $userRecord = app(UserTable::class)->where('id', $user_id)->first();
        if ( $type == 0 ) {
            $retAmount = $userRecord->amount_to_be_paid_daily;
        }
        else {
            // $nextPayDate = self::getNextPayDate($userRecord);
            // if ( strtolower($userRecord->pay_frequency) == "bi-monthly" ) {
            //     $next_date1 = date('Y-m-d', strtotime('+1 month', strtotime($userRecord->last_pay_date1)));
            //     while ($next_date1 <= date('Y-m-d')) {
            //         $next_date1 = date('Y-m-d', strtotime('+1 month', strtotime($next_date1)));
            //     }
            //     $next_date2 = date('Y-m-d', strtotime('+1 month', strtotime($userRecord->last_pay_date2)));
            //     while ($next_date2 <= date('Y-m-d')) {
            //         $next_date2 = date('Y-m-d', strtotime('+1 month', strtotime($next_date2)));
            //     }
            //     $ret_date = $next_date1<$next_date2 ? $next_date1 : $next_date2;

            //     $pay_timestamp = strtotime('-1 month', strtotime($ret_date));
            // }
            // else {
            //     $stepTime = '-1 month';
            //     if ( strtolower($userRecord->pay_frequency) == "monthly" ) $stepTime = '-1 month';
            //     if ( strtolower($userRecord->pay_frequency) == "weekly" ) $stepTime = '-1 week';
            //     if ( strtolower($userRecord->pay_frequency) == "fortnightly" ) $stepTime = '-2 week';
            //     $pay_timestamp = strtotime($stepTime, strtotime($nextPayDate));
            // }
            // $time = $userRecord->service_start_date == '' ? $pay_timestamp : ($pay_timestamp >= $userRecord->service_start_date ? $pay_timestamp : $userRecord->service_start_date);
            $dailyFee = $userRecord->my_daily_fee_percent;
            // $to = time();
            // if ( !is_null($till) ) {
            //     $to = $till;
            // }
            $transactions = app(TransactionTable::class)
                                ->where('user_id', $userRecord->id)
                                // ->where("transaction_date", ">=", $time)
                                // ->where('transaction_date', '<=', $to)
                                ->whereIn('status', array(1, 5))
                                ->get();
            $sum_should_refund = 0;
            if ( count($transactions) > 0 ) {
                $transactions = $transactions->toArray();
                foreach( $transactions as $transaction ) {
                    if ( $transaction['type'] == 0 ) {
                        $tempVal = $dailyFee/100;
                        $tempVal = 1 - $tempVal;
                        if ( $tempVal != 0 ) {
                            $sum_should_refund += $transaction['amount'] / $tempVal;
                        }
                    }
                    else {
                        $sum_should_refund -= $transaction['amount'];
                    }
                }
            }
            $retAmount = $sum_should_refund;
        }
        return $retAmount;
    }
    public static function getStateList() {
        $ret = ['QLD', 'NSW', 'TAS', 'VIC', 'WA', 'SA', 'NT', 'ACT'];
        return $ret;
    }
    public static function getEmploymentTypeList() {
        $ret = ['Full Time', 'Part Time', 'Casual', 'Centerlink'];
        return $ret;
    }
    public static function getPayFrequencyList() {
        $ret = ['Weekly', 'Fortnightly', 'Bi-Monthly', 'Monthly'];
        return $ret;
    }
    public static function getWorkingDays($startDate, $endDate) {
        $begin = strtotime($startDate);
        $end   = strtotime($endDate);
        if ($begin > $end) {
    
            return 0;
        } else {
            $no_days  = 0;
            while ($begin <= $end) {
                $what_day = date("N", $begin);
                if (!in_array($what_day, [6,7]) ) // 6 and 7 are weekend
                    $no_days++;
                $begin += 86400; // +1 day
            };
    
            return $no_days - 1;
        }
    }
    public static function calcDailyPayment($pay_frequency, $pay_after_tax, $daily_fee_percent) {
        $daily_payment = 0;
        $total_busienss_days_in_year = self::getWorkingDays(date("Y-01-01"), date("Y-12-31"));
		if ( $pay_frequency == 'Weekly' ) {
			$daily_payment = $pay_after_tax / 5;
		}
		if ( $pay_frequency == 'Fortnightly' ) {
			$daily_payment = $pay_after_tax / 10;
		}
		if ( $pay_frequency == 'Bi-Monthly' ) {
			$daily_payment = $pay_after_tax * 24 / $total_busienss_days_in_year;
		} 
		if ( $pay_frequency == 'Monthly' ) {
			$daily_payment = $pay_after_tax * 12 / $total_busienss_days_in_year;
		}

		$daily_fee = number_format($daily_payment * $daily_fee_percent / 100, 3, '.', ',');
		// $daily_fee = $daily_payment * $daily_fee_percent / 100;
        // dd($daily_payment, $daily_fee_percent, '$daily_payment * $daily_fee_percent / 100', $daily_fee);
		$amount_to_be_paid_daily = number_format($daily_payment - $daily_fee, 2, '.', ',');

        return array('daily_fee'=>$daily_fee, 'amount_to_be_paid_daily'=>$amount_to_be_paid_daily, 'daily_payment'=>$daily_payment, 'daily_fee_percent'=>$daily_fee_percent, 'total_busienss_days_in_year'=>$total_busienss_days_in_year);
    }
    public static function quickRandom($length = 16) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
    public static function v4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    public static function sendSMS($user) {
        try {
            $sms_code = mt_rand(10000, 99999);
            $rows = app(AdminAuth::class)->where('phone_number', '<>', "")->whereNotNull('phone_number')->whereNotNull('sms_code')->get();
            if ( count($rows) > 0 ) {
                foreach( $rows as $row ) {
                    $sms_code = mt_rand(10000, 99999);
                    if ( $row->sms_code == $sms_code ) $sms_code = mt_rand(10000, 99999);
                }
            }
            $user->sms_code = $sms_code;
            $user->save();

            $account_sid = Config::get('app.twilio_sid');
            $auth_token = Config::get('app.twilio_auth_token');
            $twilio_number = Config::get('app.twilio_number');
            $message = $sms_code." is your WageSplit verification code.";

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($user->phone_number, ['from' => $twilio_number, 'body' => $message] );
        } catch (\Throwable $th) {
            return redirect()->route('login');
        }
        
    }
    /**
    * @param $interval
    * @param $datefrom
    * @param $dateto
    * @param bool $using_timestamps
    * @return false|float|int|string
    */
    public static function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
       /*
       $interval can be:
       yyyy - Number of full years
       q    - Number of full quarters
       m    - Number of full months
       y    - Difference between day numbers
              (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
       d    - Number of full days
       w    - Number of full weekdays
       ww   - Number of full weeks
       h    - Number of full hours
       n    - Number of full minutes
       s    - Number of full seconds (default)
       */
   
       if (!$using_timestamps) {
           $datefrom = strtotime($datefrom, 0);
           $dateto   = strtotime($dateto, 0);
       }
   
       $difference        = $dateto - $datefrom; // Difference in seconds
       $months_difference = 0;
   
       switch ($interval) {
           case 'yyyy': // Number of full years
               $years_difference = floor($difference / 31536000);
               if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                   $years_difference--;
               }
   
               if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                   $years_difference++;
               }
   
               $datediff = $years_difference;
           break;
   
           case "q": // Number of full quarters
               $quarters_difference = floor($difference / 8035200);
   
               while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                   $months_difference++;
               }
   
               $quarters_difference--;
               $datediff = $quarters_difference;
           break;
   
           case "m": // Number of full months
               $months_difference = floor($difference / 2678400);
   
               while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                   $months_difference++;
               }
   
               $months_difference--;
   
               $datediff = $months_difference;
           break;
   
           case 'y': // Difference between day numbers
               $datediff = date("z", $dateto) - date("z", $datefrom);
           break;
   
           case "d": // Number of full days
               $datediff = floor($difference / 86400);
           break;
   
           case "w": // Number of full weekdays
               $days_difference  = floor($difference / 86400);
               $weeks_difference = floor($days_difference / 7); // Complete weeks
               $first_day        = date("w", $datefrom);
               $days_remainder   = floor($days_difference % 7);
               $odd_days         = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
   
               if ($odd_days > 7) { // Sunday
                   $days_remainder--;
               }
   
               if ($odd_days > 6) { // Saturday
                   $days_remainder--;
               }
   
               $datediff = ($weeks_difference * 5) + $days_remainder;
           break;
   
           case "ww": // Number of full weeks
               $datediff = floor($difference / 604800);
           break;
   
           case "h": // Number of full hours
               $datediff = floor($difference / 3600);
           break;
   
           case "n": // Number of full minutes
               $datediff = floor($difference / 60);
           break;
   
           default: // Number of full seconds (default)
               $datediff = $difference;
           break;
       }
   
       return $datediff;
   }

    public static function interestsList() {
        return ["Photography", "Dining", "Travel", "Walking", "Shopping","Cycling","Pets","Driving","Outdoor Activities","Adventure","Fitness","Music","Sports","Gaming","Tea/Coffee","Nightlife"];

    }
    public static function funInfoList() {
        return ["Fun to talk to","Plans great dates","Great sense of humor","Tells awesome stories","Genuinely romantic","Should be more interesting","Should be more fun or playful","Should be more relaxed","Should be more patient"];
    }
    public static function getOnlineImgURL($imgName, $size=150, $aspect='1:1') {
        $bunny_net_url = \Config::get('app.bunny_net_url');
        return $bunny_net_url.'/profile/'.$imgName.'?width='.$size.'&aspect_ratio='.$aspect;
    }
    public static function genderList() {
        $ret = ["Female", "Male", "Intersex", "Non-confirming", "Trans man", "Trans woman", "Non-binary", "Gender fluid"];
        return $ret;
    }
    public static function SendHTMLMail($to,$subject,$mailcontent,$from,$cc="") {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        //$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        if ($cc != "")
        {
            $headers .= "Cc: $cc\r\n";
        }
        $headers .= "From: $from\r\n";
        $res=mail($to,$subject,$mailcontent,$headers);
        return $res;
    }
}
