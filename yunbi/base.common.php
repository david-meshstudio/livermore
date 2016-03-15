<?php
function getSign($verb,$uri,$para,$ak,$sk) {
	ksort($para);
	$str = strtoupper($verb).'|'.$uri.'|';
	foreach ($para as $key => $value) {
		$str .= $key.'='.$value.'&';
	}
	$str = substr($str, 0, -1);
	// echo $str;
	$sign = hash_hmac('SHA256', $str, $sk);
	return $sign;
}

function getParaString($verb,$uri,$para,$ak,$sk) {
	ksort($para);
	$str = strtoupper($verb).'|'.$uri.'|access_key='.$ak;
	foreach ($para as $key => $value) {
		$str .= '&'.$key.'='.$value;
	}
	// echo $str;
	$sign = hash_hmac('SHA256', $str, $sk);
	$parastr = $str.'&signature='.$sign;
	$parastr = explode('|', $parastr);
	$parastr = $parastr[2];
	return $parastr;
}

function API_GetMemberInfo($ak,$sk) {
	$uri = '/api/v2/members/me.json';
	$para = array();
	// $para['tonce'] = str_replace('.', '', microtime(true)).'0';
	$para['tonce'] = getMillisecond();
	$parastr = getParaString('get',$uri,$para,$ak,$sk);
	$url = "https://yunbi.com".$uri."?".$parastr;
	$ret = file_get_contents($url);
	$ret = json_decode($ret, true);
	return $ret;
}

function API_GetAccountsInfo($ak,$sk) {
	$meminfo = API_GetMemberInfo($ak,$sk);
	$accounts = $meminfo['accounts'];
	$ret = array();
	if(!is_array($accounts)) {
		sleep(1);
		$meminfo = API_GetMemberInfo($ak,$sk);
		$accounts = $meminfo['accounts'];
	}
	foreach ($accounts as $row) {
		extract($row);
		$ret[$currency] = array($balance,$locked);
	}
	return $ret;
}

function API_GetPrice($market,$side) {
	$url = "https://yunbi.com/api/v2/order_book.json?market=".$market."&asks_limit=1&bids_limit=1";
	$ret = file_get_contents($url);
	$ret = json_decode($ret, true);
	if($side === 'sell') {
		$price = $ret['bids'][0]['price'];
	} else {
		$price = $ret['asks'][0]['price'];
	}
	return $price;
}

function API_GetMarketPrice() {
	$url1 = "https://yunbi.com/api/v2/order_book.json?market=ethbtc&asks_limit=1&bids_limit=1";
	$url2 = "https://yunbi.com/api/v2/order_book.json?market=btccny&asks_limit=1&bids_limit=1";
	$url3 = "https://yunbi.com/api/v2/order_book.json?market=ethcny&asks_limit=1&bids_limit=1";
	$ret = array();
	$ret1 = file_get_contents($url1);
	$ret[] = json_decode($ret1,true);
	$ret2 = file_get_contents($url2);
	$ret[] = json_decode($ret2,true);
	$ret3 = file_get_contents($url3);
	$ret[] = json_decode($ret3,true);
	return $ret;
}

function API_SendOrder($para,$ak,$sk) {
	$para['access_key'] = $ak;
	$para['order_type'] = 'limit';
	// $para['tonce'] = str_replace('.', '', microtime(true)).'0';
	$para['tonce'] = getMillisecond();
	$uri = '/api/v2/orders.json';
	$sign = getSign('post',$uri,$para,$ak,$sk);
	$para['signature'] = $sign;
	$url = 'https://yunbi.com'.$uri;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}

function API_GetOrder($id,$ak,$sk) {
	$para['access_key'] = $ak;
	$para['id'] = $id;
	// $para['tonce'] = str_replace('.', '', microtime(true)).'0';
	$para['tonce'] = getMillisecond();
	$uri = '/api/v2/order.json';
	$sign = getSign('get',$uri,$para,$ak,$sk);
	$para['signature'] = $sign;
	$url = 'https://yunbi.com'.$uri.'?';
	foreach ($para as $key => $value) {
		$url .= $key.'='.$value.'&';
	}
	$url = substr($url, 0, -1);
	$ret = file_get_contents($url);
	return $ret;
}

function API_ClearOrder($ak,$sk) {
	$para = array();
	$para['access_key'] = $ak;
	// $para['tonce'] = str_replace('.', '', microtime(true)).'0';
	$para['tonce'] = getMillisecond();
	$uri = '/api/v2/orders/clear.json';
	$sign = getSign('post',$uri,$para,$ak,$sk);
	$para['signature'] = $sign;
	$url = 'https://yunbi.com'.$uri;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}

function API_DeleteOrder($id,$ak,$sk) {
	$para = array();
	$para['access_key'] = $ak;
	$para['id'] = $id;
	// $para['tonce'] = str_replace('.', '', microtime(true)).'0';
	$para['tonce'] = getMillisecond();
	$uri = '/api/v2/order/delete.json';
	$sign = getSign('post',$uri,$para,$ak,$sk);
	$para['signature'] = $sign;
	$url = 'https://yunbi.com'.$uri;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}

// Job

function Rehit($order) {
	$price = API_GetPrice($order['market'],$order['side']);
	$rate = abs($order['price'] - $price) / $order['price'] * 100;
	$order['price'] = $order['side'] === 'buy' ? $price * 1.05 : $price * 0.95;

	if($rate > 5) {
		sendMessage(getTimeString().' reorder rate exceeded: '.$rate);
		exit();
	}
	$ret = API_SendOrder($order, ACCESS_KEY, SECRET_KEY);

	$result = json_decode($ret, true);
	if($result['id'] === null) {
		$ret = API_SendOrder($order, ACCESS_KEY, SECRET_KEY);
		$result = json_decode($ret, true);
	}
	sleep(3);
	$oid = $result['id'];
	$trade = API_GetOrder($oid, ACCESS_KEY, SECRET_KEY);
	$trade = json_decode($trade, true);
	$state = $trade['state'];
	sendMessage(getTimeString().' reorder '.$oid.', '.$rate.', '.$state);

	if($state === 'wait') {
		$ret = API_DeleteOrder($oid,ACCESS_KEY,SECRET_KEY);
		$reorder = array('side'=>$trade['side'],'price'=>$trade['price'],'market'=>$trade['market'],'volume'=>$trade['remaining_volume']);
		Rehit($reorder);
	}
}

// QYWX

function sendMessage($content) {
    if(is_array($content)) {
        $content = json_encode($content);
    }
    $ret = file_get_contents("http://meshcms.sinaapp.com/linyih/qy/lucie/sendmessage.php?code=david2014&content=".urlsafe_b64encode($content));
}

function urlsafe_b64decode($string) {
    $res = str_replace('_', '/', $string);
    $res = str_replace('-', '+', $res);
    $res = base64_decode($res);
    return $res;
}

function urlsafe_b64encode($string) {
    $res = base64_encode($string);
    $res = str_replace('+', '-', $res);
    $res = str_replace('/', '_', $res);
    return $res;
}

function getMillisecond() {
	list($t1, $t2) = explode(' ', microtime());
	return $t2.''.str_pad(floor($t1*1000).'',3,'0');
}

function getTimeString() {
	return date('YmdHis',time());
}