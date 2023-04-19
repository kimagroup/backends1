<?php
 if (isset($_GET['n'])) {
 $network = $_GET['n'];
}else{
 $network = "none";
 }
if (isset($_GET['cid'])) {
 $click = $_GET['cid'];
}else{
 $click = "none";
}
if (isset($_GET['aid'])) {
 $appid = $_GET['aid'];
}else{
 $appid = "none";
}
if (isset($_GET['pid'])) {
 $pid = $_GET['pid'];
}else{
 $pid = "none";
}
if (isset($_GET['sid'])) {
 $sid = $_GET['sid'];
}else{
 $sid = "none";
}

	function getIp(){
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
?>
<!DOCTYPE html>
<html>
<head>
</head>
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
	<script src='https://kimagrp.b-cdn.net/jquery.device.detector.js'></script>
<script>
	var d = $.fn.deviceDetector;
	var osname = d.getOsName();
	var osversion = d.getOsVersion();
</script>
<body>
<input type="text" class="ip"hidden value="<?php echo getIp();?>">
	<input type="text" class="aid"hidden value="<?php echo $appid;?>">
<input type="text" class="n"hidden value="<?php echo $network;?>">
<input type="text" class="cid"hidden value="<?php echo $click;?>">
<input type="text" class="pid"hidden value="<?php echo $pid;?>">
<input type="text" class="sid"hidden value="<?php echo $sid;?>">
	<script>
		var ip = $('.ip').val();
		var aid = $('.aid').val();
		var n = $('.n').val();
		var cid = $('.cid').val();
		var pid = $('.pid').val();
		var sid = $('.sid').val();
	$.ajax({
  url: '/saveclick',
  method: 'GET',
  data: {
    "on": osname,
	"ov":osversion,
	"ip":ip,
	"n": n,
	 "aid":aid,
	"cid":cid,
	"pid":pid,
	"sid":sid
  },
  success: function(data, textStatus, jqXHR) {

	  
	  window.location.replace(data)
	  
  }
})
	</script>
</body>
</html>

