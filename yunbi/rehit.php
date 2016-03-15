<?php
include_once('bootstraps.php');
$order = $_GET['order'];
$order = urlsafe_b64decode($order);
$order = json_decode($order, true);

$price = API_GetPrice($order['market'],$order['side']);
$rate = abs($order['price'] - $price) / $order['price'] * 100;
$order['price'] = $order['side'] === 'buy' ? $price * 1.05 : $price * 0.95;

if($rate > 5) {
	sendMessage(getTimeString().' reorder rate exceeded: '.$rate);
	exit();
}
$ret = API_SendOrder($order,ACCESS_KEY,SECRET_KEY);
// sendMessage('reorder = '.$ret);

$result = json_decode($ret, true);
if($result['id'] === null) {
	$ret = API_SendOrder($order,ACCESS_KEY,SECRET_KEY);
	$result = json_decode($ret, true);
}
sleep(3);
$oid = $result['id'];
$trade = API_GetOrder($oid,ACCESS_KEY,SECRET_KEY);
$trade = json_decode($trade, true);
$state = $trade['state'];
sendMessage(getTimeString().' reorder '.$oid.', '.$rate.', '.$state);

if($state === 'wait') {
	$ret = API_DeleteOrder($oid,ACCESS_KEY,SECRET_KEY);
	$reorder = array('side'=>$trade['side'],'price'=>$trade['price'],'market'=>$trade['market'],'volume'=>$trade['remaining_volume']);
	$ostr = json_encode($reorder);
	$res = file_get_contents('http://livermore.sinaapp.com/yunbi/rehit.php?order='.urlsafe_b64encode($ostr));
}