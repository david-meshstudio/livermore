<?php
include_once('bootstraps.php');
$kv = new SaeKV();
$kv->init();

$market = API_GetMarketPrice();
$benifits = getFishBenifitsFix($market,1);

$max = array();
foreach ($benifits as $key => $value) {
	echo $value['rate'].',';
	if($value['good'] && $value['rate'] > 1) {
		if(count($max) === 0 || $max['rate'] < $value['rate']) {
			$max['key'] = $key;
			$max['value'] = $value;
			$max['rate'] = $value['rate'];
		}
	}
}
if(count($max) === 0) exit();
$key = $max['key'];
$value = $max['value'];

$orders = $value['orders'];
$fishOrder = array();
$hitOrders = array();
if($key === 'M1' || $key === 'M2') {

} else if($key === 'M3' || $key === 'M4') {
	
} else if($key === 'M5' || $key === 'M6') {
	foreach ($orders as $order) {
		if($order['market'] === 'ethbtc') {
			$fishOrder = $order;
		} else {
			$hitOrders[] = $order;
		}
	}
}
sendMessage(getTimeString().' fish catch '.$key.', '.$value['rate'].', order='.json_encode($fishOrder));
$ret = API_SendOrder($fishOrder, ACCESS_KEY, SECRET_KEY);
$result = json_decode($ret, true);
if($result['id'] === null) {
	$ret = API_SendOrder($fishOrder, ACCESS_KEY, SECRET_KEY);
	$result = json_decode($ret, true);
}
$oid = $result['id'];
$sret = '';
// 5 times check
sleep(1);
list($remain, $executed) = CheckFishOrder($oid);
if($executed > 0) {
	$ret = API_DeleteOrder($oid, ACCESS_KEY, SECRET_KEY);
	if($remain > 0) {
		$hitOrders = RefreshFishOrder($hitOrders, $executed);
	}
	$sret .= MakeOrderDone($hitOrders[0]);
	$sret .= MakeOrderDone($hitOrders[1]);
	sendMessage(getTimeString().' fish catch '.$key.', '.$value['rate'].', state='.$sret);
} else {
	sleep(1);
	list($remain, $executed) = CheckFishOrder($oid);
	if($executed > 0) {
		$ret = API_DeleteOrder($oid, ACCESS_KEY, SECRET_KEY);
		if($remain > 0) {
			$hitOrders = RefreshFishOrder($hitOrders, $executed);
		}
		$sret .= MakeOrderDone($hitOrders[0]);
		$sret .= MakeOrderDone($hitOrders[1]);
		sendMessage(getTimeString().' fish catch '.$key.', '.$value['rate'].', state='.$sret);
	} else {
		sleep(1);
		list($remain, $executed) = CheckFishOrder($oid);
		if($executed > 0) {
			$ret = API_DeleteOrder($oid, ACCESS_KEY, SECRET_KEY);
			if($remain > 0) {
				$hitOrders = RefreshFishOrder($hitOrders, $executed);
			}
			$sret .= MakeOrderDone($hitOrders[0]);
			$sret .= MakeOrderDone($hitOrders[1]);
			sendMessage(getTimeString().' fish '.$key.', '.$value['rate'].', state='.$sret);
		} else {
			sleep(1);
			list($remain, $executed) = CheckFishOrder($oid);
			if($executed > 0) {
				$ret = API_DeleteOrder($oid, ACCESS_KEY, SECRET_KEY);
				if($remain > 0) {
					$hitOrders = RefreshFishOrder($hitOrders, $executed);
				}
				$sret .= MakeOrderDone($hitOrders[0]);
				$sret .= MakeOrderDone($hitOrders[1]);
				sendMessage(getTimeString().' fish '.$key.', '.$value['rate'].', state='.$sret);
			} else {
				sleep(1);
				list($remain, $executed) = CheckFishOrder($oid);
				if($executed > 0) {
					$ret = API_DeleteOrder($oid, ACCESS_KEY, SECRET_KEY);
					if($remain > 0) {
						$hitOrders = RefreshFishOrder($hitOrders, $executed);
					}
					$sret .= MakeOrderDone($hitOrders[0]);
					$sret .= MakeOrderDone($hitOrders[1]);
					sendMessage(getTimeString().' fish '.$key.', '.$value['rate'].', state='.$sret);
				} else {
					$ret = API_DeleteOrder($oid, ACCESS_KEY, SECRET_KEY);
					sendMessage(getTimeString().' fish failed '.$key.', '.$value['rate']);
				}
			}
		}
	}
}

// $sw = $kv->get('fsw');
// if($sw === 'on') {
// 	sleep(1);
// 	$queue = new SaeTaskQueue('task2');
// 	$queue->addTask("http://livermore.sinaapp.com/yunbi/fish.php");
// 	$ret = $queue->push();
// 	$c = new SaeCounter();
// 	$c->incr('c2');	
// }

function CheckFishOrder($oid) {
	$trade = API_GetOrder($oid, ACCESS_KEY, SECRET_KEY);
	$trade = json_decode($trade, true);
	$remain = $trade['remaining_volume'];
	$executed = $trade['executed_volume'];
	return array($remain,$executed);
}

function RefreshFishOrder($orders,$amount) {
	if($orders[0]['market'] === 'ethcny') {
		$price = $orders[2]['price'];
		$input = $amount * $price;
		$orders[1]['volume'] = $input;
		$price2 = $orders[1]['price'];
		$price3 = $orders[0]['price'];
		$orders[0]['volume'] = floor($input * $price2 / $price3 * 10000) / 10000;
	} else {
		$price = $orders[0]['price'];
		$input = $amount * $price;
		$orders[1]['volume'] = $input;
		$price2 = $orders[1]['price'];
		$price3 = $orders[2]['price'];
		$orders[2]['volume'] = floor($input * $price2 / $price3 * 10000) / 10000;
	}
	return $orders;
}

function MakeOrderDone($order) {
	$order_result = array();
	$reorders = array();
	$ret = API_SendOrder($order, ACCESS_KEY, SECRET_KEY);
	$result = json_decode($ret, true);
	if($result['id'] === null) {
		$ret = API_SendOrder($order, ACCESS_KEY, SECRET_KEY);
		$result = json_decode($ret, true);
	}
	$oid = $result['id'];
	$trade = API_GetOrder($oid, ACCESS_KEY, SECRET_KEY);
	$trade = json_decode($trade, true);
	$state = $trade['state'];
	$sret = '';
	if($state === 'wait') {
		$reorders[] = array('side'=>$trade['side'],'price'=>$trade['price'],'market'=>$trade['market'],'volume'=>$trade['remaining_volume']);
		$sret .= $state.',';
		$delids[] = $oid;
	} else if($state != 'done') {
		$sret .= $oid.',';
	} else {
		$sret .= $state.',';
		$delids[] = $oid;
	}
	if(count($reorders) > 0) {
		// $ret = API_ClearOrder(ACCESS_KEY, SECRET_KEY);
		foreach ($delids as $doid) {
			$ret = API_DeleteOrder($doid, ACCESS_KEY, SECRET_KEY);
		}
		foreach ($reorders as $reorder) {
			// $ret = API_SendOrder($reorder,ACCESS_KEY,SECRET_KEY);
			// sendMessage('reorder = '.$ret);
			$ostr = json_encode($reorder);
			// $res = file_get_contents('http://livermore.sinaapp.com/yunbi/call.rehit.php?order='.urlsafe_b64encode($ostr));
			$res = file_get_contents('http://livermore.sinaapp.com/yunbi/rehit.php?order='.urlsafe_b64encode($ostr));
		}
	}
	return $sret;
}