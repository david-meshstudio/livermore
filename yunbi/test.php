<?php
include_once('bootstraps.php');
$url1 = "https://yunbi.com/api/v2/order_book.json?market=ethbtc&asks_limit=1&bids_limit=1";
$url2 = "https://yunbi.com/api/v2/order_book.json?market=btccny&asks_limit=1&bids_limit=1";
$url3 = "https://yunbi.com/api/v2/order_book.json?market=ethcny&asks_limit=1&bids_limit=1";
// $accesskey = "A2LhBx9txuDI2iqStq9OZxkwELBg13rqqvCB25rt";
// $secretkey = "UudQYWta1A5u291qlRJGxWQFGESgPo3c6oY6Xd2l";
$ak = "DQxqaaoFz2IymBEj9N463nvUBMkwXx4bep0zdYva";
$sk = "yDALXSsQOBWnDYoilqYadJkuhe9x5YaJ35GDIKUR";

$ret1 = file_get_contents($url1);
$ret1 = json_decode($ret1,true);
$ret2 = file_get_contents($url2);
$ret2 = json_decode($ret2,true);
$ret3 = file_get_contents($url3);
$ret3 = json_decode($ret3,true);

// $id = '69551873';
// $para = array('id'=>$id);
// $parastr = getSign('get','/api/v2/order.json',$para,$ak,$sk);
// $url4 = "https://yunbi.com/api/v2/order.json?".$parastr;
// echo $url4;
// echo '<br>';
// echo file_get_contents($url4);
// echo '<br>';
// echo floor(3.1415926 * 10000) / 10000;
$origin_eth = 10;

$p1 = $ret1['bids'][0]['price'];
$t1 = $ret1['bids'][0]['volume'];
echo 'ethbtc,'.$p1.','.$t1.',';
if($t1 >= $origin_eth) {
	$m1 = $origin_eth * $p1;
	$ct = $origin_eth;
	$remain_eth = 0;
} else {
	$m1 = $t1 * $p1;
	$ct = $t1;
	$remain_eth = $origin_eth - $t1;
}
echo $m1.'<br>';

$market[] = 'ethbtc';
$side[] = 'sell';
$volume[] = $ct;
$price[] = $p1;

$p2 = $ret2['bids'][0]['price'];
$t2 = $ret2['bids'][0]['volume'];
echo 'btccny,'.$p2.','.$t2.',';
if($t2 >= $m1) {
	$m2 = $p2 * $m1;
} else {
	echo 'not enough btc buyer';
	exit();
}
echo $m2.'<br>';

$market[] = 'btccny';
$side[] = 'sell';
$volume[] = $m1;
$price[] = $p2;

$p3 = $ret3['asks'][0]['price'];
$t3 = $ret3['asks'][0]['volume'];
echo 'ethcny,'.$p3.','.$t3.',';
if($t3 * $p3 >= $m2) {
	$m3 = $m2 / $p3;
} else {
	echo 'not enough eth seller';
	exit();
}
echo $m3.'<br>';

$market[] = 'ethcny';
$side[] = 'buy';
$volume[] = $m2;
$price[] = $p3;

$ret = $remain_eth + $m3;
//echo $ret.',';
$r1 = ($ret - $origin_eth) / $origin_eth * 100;
echo $r1;
if($r1 >= 0.5) {
	$mysql = new SaeMysql();
	$sql = "insert into `test_result` (`rate`) values ('".$r1."');";
	$ret = $mysql->runSql($sql);
	sendMessage('catch '.$r1);
}

/*
$p3 = $ret3['bids'][0]['price'];
$t3 = $ret3['bids'][0]['volume'];
if($t3 >= $origin_eth) {
	$m1 = $origin_eth * $p3;
	$remain_eth = 0;
} else {
	$m1 = $t3 * $p3;
	$remain_eth = $origin_eth - $m1;
}

$p2 = $ret2['asks'][0]['price'];
$t2 = $ret2['asks'][0]['volume'];
if($t2 * $p2 >= $m1) {
	$m2 = $m1 / $p2;
} else {
	echo 'not enough btc seller';
	exit();
}

$p1 = $ret1['asks'][0]['price'];
$t1 = $ret1['asks'][0]['volume'];
if($t1 * $p1 >= $m2) {
	$m3 = $m2 / $p1;
} else {
	echo 'not enough eth seller';
	exit();
}
$ret = $remain_eth + $m3;
$r1 = ($ret - $origin_eth) / $origin_eth * 100;
echo $r1;
*/

// function getSign($verb,$uri,$para,$ak,$sk) {
// 	$str = strtoupper($verb).'|'.$uri.'|access_key='.$ak;
// 	foreach ($para as $key => $value) {
// 		$str .= '&'.$key.'='.$value;
// 	}
// 	echo $str;
// 	$sign = hash_hmac('SHA256', $str, $sk);
// 	return $sign;
// }

// function getParaString($verb,$uri,$para,$ak,$sk) {
// 	$str = strtoupper($verb).'|'.$uri.'|access_key='.$ak;
// 	foreach ($para as $key => $value) {
// 		$str .= '&'.$key.'='.$value;
// 	}
// 	echo $str;
// 	$sign = hash_hmac('SHA256', $str, $sk);
// 	$parastr = $str.'&signature='.$sign;
// 	$parastr = explode('|', $parastr);
// 	$parastr = $parastr[2];
// 	return $parastr;
// }

// function sendOrder($market,$side,$volume,$price,$ak,$sk) {
// 	$para['market'] = $market;
// 	$para['order_type'] = '';
// 	$para['price'] = $price;
// 	$para['side'] = $side;
// 	$para['tonce'] = str_replace('.', '', microtime(true)).'0';
// 	$para['volume'] = $volume;
// 	$uri = '/api/v2/orders.json';
// 	$sign = getSign('post',$uri,$para,$ak,$sk);
// 	$para['signature'] = $sign;
// 	$url = 'https://yunbi.com'.$uri;

// 	$ch = curl_init();
// 	curl_setopt($ch, CURLOPT_URL, $url);
// 	curl_setopt($ch, CURLOPT_POST, 1);
// 	curl_setopt($ch, CURLOPT_HEADER, 0);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
// 	$ret = curl_exec($ch);
// 	curl_close($ch);
// 	echo $ret;
// }