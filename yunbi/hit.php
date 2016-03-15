<?php
include_once('bootstraps.php');

$kv = new SaeKV();
$kv->init();
$market = API_GetMarketPrice();
$benifits = getMethodBenifitsFix3($market,1,0.02,50);

$order_result = array();
$reorders = array();
$ret = array();
foreach ($benifits as $key => $value) {
	if($value['good'] && $value['rate'] > 0.1) {
		$ret[$key] = $value;
	}
}
if(count($ret) > 0) {
	foreach ($ret as $key => $value) {
		if($value['good'] && $value['rate'] > 0.1) {
			$orders = $value['orders'];
			foreach ($orders as $order) {
				$ret = API_SendOrder($order,ACCESS_KEY,SECRET_KEY);
				$result = json_decode($ret, true);
				if($result['id'] === null) {
					$ret = API_SendOrder($order,ACCESS_KEY,SECRET_KEY);
					$result = json_decode($ret, true);
				}
				$order_result[] = $result;
			}
			sleep(3);
			$sret = '';
			$delids = array();
			foreach ($order_result as $result) {
				$oid = $result['id'];
				$trade = API_GetOrder($oid,ACCESS_KEY,SECRET_KEY);
				$trade = json_decode($trade, true);
				$state = $trade['state'];
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
			}
			sendMessage(getTimeString().' catch '.$key.', '.$value['rate'].', state='.$sret);
			if(count($reorders) > 0) {
				foreach ($delids as $oid) {
					$ret = API_DeleteOrder($oid, ACCESS_KEY, SECRET_KEY);
				}
				foreach ($reorders as $reorder) {
					// $ostr = json_encode($reorder);
					// $res = file_get_contents('http://livermore.sinaapp.com/yunbi/rehit.php?order='.urlsafe_b64encode($ostr));
					$res = Rehit($reorder);
				}
			}
			break;
		}
	}
} else {
	$yp = $kv->get('yp');
	if($yp === 'on') {
		$url = 'http://livermore.au-syd.mybluemix.net/yp/run.php';
		$ret = file_get_contents($url);
	}
}

$sw = $kv->get('sw');
if($sw === 'on') {
	sleep(1);
	$queue = new SaeTaskQueue('task1');
	$queue->addTask("http://livermore.sinaapp.com/yunbi/hit.php");
	$ret = $queue->push();
	$c = new SaeCounter();
	$c->incr('c1');	
}