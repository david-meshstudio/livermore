<?php
include_once('bootstraps.php');

// $ret = API_ClearOrder(ACCESS_KEY, SECRET_KEY);

$mysql = new SaeMysql();
// $sqlArray = array();
// $accounts = API_GetAccountsInfo(ACCESS_KEY, SECRET_KEY);

// $cny = $accounts['cny'][0];
// if($cny > 10) {
// 	$input = $cny;
// 	$market = API_GetMarketPrice();
// 	list($rate,$total,$output,$remain) = getMarketAnalysis('ce',$market,$input);
// 	list($marketstr,$side) = getMarketSide('ce');
// 	// $order1 = genOrderPara($marketstr,$side,$side === 'buy' ? floor(($input - $remain) / $rate * 10000) / 10000 : ($input - $remain),$rate);
// 	// $ret = API_SendOrder($order1,ACCESS_KEY,SECRET_KEY);
// 	// sendMessage($ret);
// 	$sqlArray[] = "insert into `order_record` (`order`,`result`) value ('".json_encode($order1)."','".$ret."');";
// }

// $btc = $accounts['btc'][0];
// if($btc > 0.01) {
// 	$input = $btc;
// 	$market = API_GetMarketPrice();
// 	list($rate,$total,$output,$remain) = getMarketAnalysis('be',$market,$input);
// 	list($marketstr,$side) = getMarketSide('be');
// 	// $order2 = genOrderPara($marketstr,$side,$side === 'buy' ? floor(($input - $remain) / $rate * 10000) / 10000 : ($input - $remain),$rate);
// 	// $ret = API_SendOrder($order2,ACCESS_KEY,SECRET_KEY);
// 	// sendMessage($ret);
// 	$sqlArray[] = "insert into `order_record` (`order`,`result`) value ('".json_encode($order2)."','".$ret."');";
// }

$accounts = API_GetAccountsInfo(ACCESS_KEY, SECRET_KEY);
$market = API_GetMarketPrice();
$benifits = getMethodBenifits($market,$accounts);
var_dump($benifits);

foreach ($benifits as $key => $value) {
	if($value['good'] && $value['rate'] > 0.5) {
		$sql = "insert into `test_result2` (`rate`,`benifit`) values ('".$value['rate']."','".$key."|".json_encode($value['orders'])."');";
		$ret = $mysql->runSql($sql);
		$orders = $value['orders'];
		foreach ($orders as $order) {
			// $ret = API_SendOrder($order,ACCESS_KEY,SECRET_KEY);
			// sendMessage($ret);
			// usleep(200);
			$sqlArray[] = "insert into `order_record` (`order`,`result`) value ('".json_encode($order)."','".$ret."');";
			$retobj = json_decode($ret,true);
			if($retobj['error'] != null) {

			}
		}
		break;
	}
}

// foreach ($sqlArray as $sql) {
// 	$mysql->runSql($sql);
// }