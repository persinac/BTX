<?php
parse_str(parse_url(curPageURL(), PHP_URL_QUERY), $output);
$apikey = $output['apikey'];
//'e281fee0429e4228b9a76f63cc842f12d';
$apisecret = $output['apisecret']; 
//='a131b56ec7f24696a74c6b6da853d1eb';
$nonce=time();
$uri='https://bittrex.com/api/v1.1/account/getbalances?apikey='.$apikey.'&nonce='.$nonce;
$sign=hash_hmac('sha512',$uri,$apisecret);
//$ch = curl_init($uri);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
$stuff = array();
$stuff[] = ["sign"=>$sign];
//$stuff[] = ["uri"=>$uri);
var_dump($sign);

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
