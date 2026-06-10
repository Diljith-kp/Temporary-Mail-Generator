<?php
error_reporting(0);

function RandomNumber($length){$str="";for($i=0;$i<$length;$i++){$str.=mt_rand(0,9);}return$str;}
function rando($length){$characters="1234567890abcdefghijklmnopqrstuvwxyz";$charactersLength=strlen($characters);$randomString="";for($i=0;$i<$length;$i++){$randomString.=$characters[rand(0,$charactersLength-1)];}return$randomString;}

if(isset($_REQUEST["mid"])){
    $mid = $_REQUEST["mid"];
    $hh = $_REQUEST["hh"];
    $url1 = "https://10minutemail.net/mail.api.php?mailid=$mid&sessionid=$hh";
    $headers = ["Content-Type:application/x-www-form-urlencoded","Host: 10minutemail.net","Connection: Keep-Alive","Accept-Encoding: gzip","User-Agent: okhttp/3.12.1"];
    $ch = curl_init(); curl_setopt($ch,CURLOPT_URL,$url1); curl_setopt($ch,CURLOPT_HEADER,0); curl_setopt($ch,CURLOPT_HTTPHEADER,$headers); curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0); curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0); curl_setopt($ch,CURLOPT_ENCODING,"gzip"); curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE); curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); $one = curl_exec($ch); curl_close($ch);
    $json = json_decode($one,true); $e = $json["body"][0]["body"]; $id = $json["from"]; $sub = $json["subject"]; $time = $json["datetime2"]; $c = $json["mail_list"]; $mid = $json["mail_id"];
} elseif(isset($_REQUEST["hh"])){
    $hh = $_REQUEST["hh"];
    $url1 = "https://10minutemail.net/address.api.php?sessionid=$hh&_=1582440128496";
    $headers = ["Content-Type:application/x-www-form-urlencoded","Host: 10minutemail.net","Connection: Keep-Alive","Accept-Encoding: gzip","User-Agent: okhttp/3.12.1"];
    $ch = curl_init(); curl_setopt($ch,CURLOPT_URL,$url1); curl_setopt($ch,CURLOPT_HEADER,0); curl_setopt($ch,CURLOPT_HTTPHEADER,$headers); curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0); curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0); curl_setopt($ch,CURLOPT_ENCODING,"gzip"); curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE); curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); $one = curl_exec($ch); curl_close($ch);
    $json = json_decode($one,true); $e = $json["mail_get_mail"]; $id = $json["mail_list"][0]["from"]; $sub = $json["mail_list"][0]["subject"]; $time = $json["mail_list"][0]["datetime2"]; $c = $json["mail_list"]; $z = count($c);
} else {
    $i8 = RandomNumber(8); $device = RandomNumber(16); $i30 = RandomNumber(30); $imei = rando(16); $b = RandomNumber(3); $i42 = RandomNumber(42); $imo = RandomNumber(15); $l = time();
    $hh = "21b6e$imei$i8$b";
    $url1 = "https://10minutemail.net/address.api.php?new=1&sessionid=$hh&_=$l";
    $headers = ["Content-Type:application/x-www-form-urlencoded","Host: 10minutemail.net","Connection: Keep-Alive","Accept-Encoding: gzip","User-Agent: okhttp/3.12.1"];
    $ch = curl_init(); curl_setopt($ch,CURLOPT_URL,$url1); curl_setopt($ch,CURLOPT_HEADER,0); curl_setopt($ch,CURLOPT_HTTPHEADER,$headers); curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0); curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0); curl_setopt($ch,CURLOPT_ENCODING,"gzip"); curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE); curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); $one = curl_exec($ch); curl_close($ch);
    $json = json_decode($one,true);
    
    // Check if JSON decoding was successful and the required data exists
    if ($json && isset($json['mail_get_mail']) && isset($json['session_id'])) {
        $e = $json["mail_get_mail"];
        $s = $json["session_id"];
    } else {
        // Fallback for when the API call fails
        $e = "API Error: Could not generate a new email.";
        $s = null;
    }
}
?>