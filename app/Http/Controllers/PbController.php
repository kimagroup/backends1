<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Redirect;
use Carbon\Carbon;
class PbController extends Controller
{
	 public function clickd()
    {
		return 1;
    }
	 public function redirec(Request $request)
    {
		 
		 $url = $request->urlddd;
		 		

 return redirect()->to($url);  

    }
	 public function postbacktest(Request $request)
    {
	
		 $clickid = $request->cid;
	$event = $request->event;
		 $insertconverion = DB::table('conversions')->insert([
					'cid' => $clickid,
					'event' => $event,
				]);
    }
	
	 public function postback(Request $request)
    {
	$appid = $request->aid;
	$clickid = $request->cid;
	$ip = $this->getIp();
	$event = $request->event;
	$getdate = DB::table('clicks')->where('ip', $ip)->where('cid', $clickid)->first();
	$getevent = DB::table('events')->where('name', $event)->where('network_id', $getdate->network)->first();
	$getnetwork = DB::table('networks')->where('id', $getdate->network)->first();
	$timelimit = $getevent->expiration;
	//Non-payable conversion postback
	if($getevent->type == 0){
		if($getnetwork->install == 1){
		//Macros handler
		$url = $getnetwork->postback;
		$url = str_replace('{clickid}',$clickid,$url);
		$url = str_replace('{eventname}',$event,$url);
		$response = Http::get($url);
				if($response->successful()){
				$insertlog = DB::table('postback_logs')->insert([
					'postback' => $url,
					'network' => $getnetwork->id,
					'status' => 1,
				]);
					if($insertlog){
					return 200;
					}else{
					return "Processing error";
					}
				}else{
					$insertlog = DB::table('postback_logs')->insert([
					'postback' => $url,
					'network' => $getnetwork->id,
					'status' => 2,
				]);
					if($insertlog){
					return 200;
					}else{
					return "Processing error";
					}
				}
		
		}
	//Payable conversion postback
	}elseif($getevent->type == 1){
		$to = Carbon::now();
        $from = Carbon::parse($getdate->date);
        $diff = $to->diffInHours($from);
			if($diff <= $getevent->expiration){
				$url = $getnetwork->postback;
				$url = str_replace('{clickid}',$clickid,$url);
				$url = str_replace('{eventname}',$event,$url);
				$response = Http::get($url);
				if($response->successful()){
				$insertlog = DB::table('postback_logs')->insert([
					'postback' => $url,
					'network' => $getnetwork->id,
					'status' => 1,
				]);
					if($insertlog){
					return 200;
					}else{
					return "Processing error";
					}
				}else{
					$insertlog = DB::table('postback_logs')->insert([
					'postback' => $url,
					'network' => $getnetwork->id,
					'status' => 2,
				]);
					if($insertlog){
					return 200;
					}else{
					return "Processing error";
					}
				}
		
			}else{
			return "Expired time";
			}
	
	}
		
    }
	 public function redirect(Request $request)
    {
		//$url = $request->url;
		//return Redirect::to($url);
		 $time =  Carbon::now()->format('Y-m-d H:s:i');
		  $to = Carbon::createFromFormat('Y-m-d H:s:i', '2015-5-5 3:30:34');
        $from = Carbon::createFromFormat('Y-m-d H:s:i', '2015-5-5 9:30:34');
  
        $diff_in_hours = $to->diffInHours($from);
              
        dd($diff_in_hours);
		 return  $time;
    }
	 public function saveclick(Request $request)
    {
		 $date =  Carbon::now()->format('Y-m-d H:s:i');
		$osname = $request->on;
		 $osversion = $request->ov;
		 $osv = $osname.$osversion;
		 $os = str_replace("Linux","",$osv);
		 $ip = $request->ip;
		 $aid = $request->aid;
		 $cid = $request->cid;
		 $pid = $request->pid;
		 $sid = $request->sid;
		 $network = $request->n;

		$createclick = DB::table('clicks')->insert([
		'app_id' => $aid,
    	'cid' => $cid,
   		'network' => $network,
		'pid' => $pid,
		'sid' => $sid,
		'ip' => $ip,
		'os' => $os,
		'converted' => 0,
		'date' => $date
		]);
		 if($createclick){
			 $getapplink = DB::table('apps')->where('id', $aid)->first();
		 		return $getapplink->link_android;
		 }
    }
    public function checkclick(Request $request)
    {
		$ip = $this->getIp();
$appid = $request->aid;
	$data = $request->n;
$deviced = strtok($data, '/');
		$device = trim($deviced);

	
	
	
	$getclick = DB::table('clicks')->where('ip', $ip)->where('os', $device)->where('app_id',$appid)->first();
	if($getclick){
		$cid = $getclick->cid;
		return $cid;
	}else{
		return "none";
	}
	
    }
	public function getIp(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
    return request()->ip(); // it will return the server IP if the client IP is not found using this method.
}
}
