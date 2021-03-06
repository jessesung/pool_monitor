<?php
session_start();

// refresh data after one minute
if(isset($_SESSION[$_GET["site"]."timestamp"]) && ((time()-$_SESSION[$_GET["site"]."timestamp"]) < 60))
{
	echo $_SESSION[$_GET["site"]];
	exit(0);
}

$json = file_get_contents('./sites.js');
$json = str_replace('var APIS = ','' ,$json);
$sites = json_decode($json);

$datatype = 0;
$apiurl = "";
foreach( $sites as $site)
{
	if($site->site == $_GET["site"])
	{
		$apiurl = $site->apiurl.$_GET["key"];
		$datatype = $site->datatype;
		break;
	}
}

$data = get_data($apiurl, $datatype);
$_SESSION[$_GET["site"]] = $data;
$_SESSION[$_GET["site"]."timestamp"] = time();

echo $data;

function get_data($apiurl, $datatype)
{
	$ch = null;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$res = "";
	if($datatype == "1")
	{
		curl_setopt($ch, CURLOPT_URL, $apiurl);
		$res = curl_exec($ch);
	}
	else if($datatype == "2")
	{
	}
	else if($datatype == "3")
	{
	}
	else if($datatype == "4")
	{
		$data = array();

		$url = $apiurl."&action=getuserstatus";
		curl_setopt($ch, CURLOPT_URL, $url);
		$res = curl_exec($ch);
		$data = json_decode($res);

		$url = $apiurl."&action=getuserbalance";
		curl_setopt($ch, CURLOPT_URL, $url);
		$res = curl_exec($ch);
		$data = (object)array_merge((array)$data, (array)json_decode($res));

		$url = $apiurl."&action=getuserworkers";
		curl_setopt($ch, CURLOPT_URL, $url);
		$res = curl_exec($ch);
		$data = (object)array_merge((array)$data, (array)json_decode($res));

		$res = json_encode($data);
	}

	return $res;
}
?>
