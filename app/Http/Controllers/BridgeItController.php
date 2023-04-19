<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use DB;
use Hash;
class BridgeItController extends Controller
{
	
	 public function index()
    {
	return "Not authorized.";
    }
	public function check(Request $request)
    {
	return 1;	
    }
	
	public function session(Request $request)
    {
	
		$username = Str::random(70);
				//$username = mt_rand(1, 9);
		
		$ip = $this->getIp();
		$checkid = DB::table('members')->where('username',$username)->first();
		if($checkid){
		
		}else{
		$createuser = DB::table('members')->insert([
    	'username' => $username,
		'email' => 'none',
		'coins' => 0,
		'energy' => 1,
		'diamonds' => 0,
		'pro' => 0,
		'clvl' => 1,
		'olvl' => 1,
		'ip' => $ip,
		]);
		if($createuser){
		return $username;
		}else{
		return "issue";
		}
		}
			
    }
	public function update(Request $request)
    {
	$username = $request->u;
	$input = $request->t;
	if($input and $username){
	switch($input){
		//Bonus handler
		case "bonus":
		$coins = $request->c;
		$energy = $request->e;
		$bonusupdate = DB::table('members')
              ->where('username', $username)
              ->update(['coins' => $coins,'energy' => $energy]);
		if($bonusupdate){
			return "bonusupdated";
		}else{
			return "bonusissue";
		}
		break;
		//Update PRO handler
		case "pro":
		$proupdate = DB::table('members')
              ->where('username', $username)
              ->update(['pro' => 1]);
		if($proupdate){
			return "proupdated";
		}else{
			return "proissue";
		}
		break;
		//Update Level handler
		case "level":
		$clvl = $request->cl;
		$olvl = $request->ol;
		$levelupdate = DB::table('members')
              ->where('username', $username)
              ->update(['clvl' => $clvl,'olvl' => $olvl, 'energy' => DB::raw('GREATEST(energy - 1, 0)')]);
		if($levelupdate){
			return "levelupdated";
		}else{
			return "levelissue";
		}
		break;
		//Update Coins handler
		case "coins":
		$coins = $request->c;
		$balanceupdate = DB::table('members')
              ->where('username', $username)
              ->update(['coins' => $coins]);
		if($balanceupdate){
			return "balanceupdated";
		}else{
			return "balanceissue";
		}
		break;
		//Update Energy handler
		case "energy":
		$energy = $request->e;
		$energyupdate = DB::table('members')
              ->where('username', $username)
              ->update(['energy' => $energy]);
		if($energyupdate){
			return "energyupdated";
		}else{
			return "energyissue";
		}
		break;
	}
	}
	$getuser = DB::table('members')->where('username', $username)->first();

    }
	public function info(Request $request)
    {
	$username = $request->u;
	$checkemail = DB::table('members')->where('username', $username)->value('email');
	if($checkemail == "none"){
	$emailgiven = 0;
	}else{
	$emailgiven = 1;
	}
	$getuser = DB::table('members')->where('username', $username)->first();

		return response()->json(['status' => 'ok', 'coins' => $getuser->coins, 'energy' => $getuser->energy, 'diamonds' => $getuser->diamonds, 'clvl' => $getuser->clvl, 'olvl' => $getuser->olvl, 'pro' => $getuser->pro, 'emailgiven' => $emailgiven]);
    }
    public function addemail(Request $request)
    {
        $username = $request->username;
		$email = $request->email;
		$contains = Str::contains($email, '@');
		if($contains){
		$addemail = DB::table('members')
              ->where('username', $username)
              ->update(['email' => $email]);
			if($addemail){
				return "emailupdated";
			}else{
				return "emailissue";
			}
		}else{
			return "emailnoexist";
		}
	
 	}
	public function registration(Request $request)
    {
        $username = $request->username;
		$ip = $request->ip();
		//Check if user exists
		$user = DB::table('members')->where('username',$username)->first();
		//User exists
		if($user){
			return "exists";
		}else{
		$createuser = DB::table('members')->insert([
    	'username' => $username,
		'email' => 'none',
		'coins' => 0,
		'energy' => 0,
		'pro' => 0,
		'clvl' => 1,
		'olvl' => 1,
		'ip' => $ip,
		]);
		if($createuser){
		return "ok";
		}else{
		return "issue";
		}
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
