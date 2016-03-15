<?php
include_once('bootstraps.php');

sleep(rand(1,30));

$purl = "https://poloniex.com/public?command=returnOrderBook&currencyPair=BTC_ETH&depth=1";
$pret = file_get_contents($purl);
$pinfo = json_decode($pret, true);
if($pinfo['asks'] === null) exit();

$market = API_GetMarketPrice();
$market[0]['asks'][0]['price'] = $pinfo['asks'][0][0];
$market[0]['asks'][0]['volume'] = $pinfo['asks'][0][1];
$market[0]['bids'][0]['price'] = $pinfo['bids'][0][0];
$market[0]['bids'][0]['volume'] = $pinfo['bids'][0][1];

$benifits = getMethodBenifitsFix3($market,1,0.018,50);
$res = array();
$res['M1'] = array($benifits['M1']['good'],$benifits['M1']['rate']);
$res['M2'] = array($benifits['M2']['good'],$benifits['M2']['rate']);
$res['M3'] = array($benifits['M3']['good'],$benifits['M3']['rate']);
$res['M4'] = array($benifits['M4']['good'],$benifits['M4']['rate']);
$res['M5'] = array($benifits['M5']['good'],$benifits['M5']['rate']);
$res['M6'] = array($benifits['M6']['good'],$benifits['M6']['rate']);
echo json_encode($res);

$mysql = new SaeMysql();
foreach ($res as $key => $value) {
	if($value[0] && $value[1] > 1) {
		$sql = "insert into `yp_check` (`rate`) values ('".$key."=".$value[1]."');";
		$mysql->runSql($sql);
	}
}