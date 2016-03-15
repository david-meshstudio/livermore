<?php
include_once('bootstraps.php');
$market = API_GetMarketPrice();
$benifits = getMethodBenifitsFix3($market,1,0.018,50);
$res = array();
$res['M1'] = array($benifits['M1']['good'],$benifits['M1']['rate']);
$res['M2'] = array($benifits['M2']['good'],$benifits['M2']['rate']);
$res['M3'] = array($benifits['M3']['good'],$benifits['M3']['rate']);
$res['M4'] = array($benifits['M4']['good'],$benifits['M4']['rate']);
$res['M5'] = array($benifits['M5']['good'],$benifits['M5']['rate']);
$res['M6'] = array($benifits['M6']['good'],$benifits['M6']['rate']);
echo json_encode($res);

echo '<br>';

$c = new SaeCounter();
$ret = $c->get('c1');
var_dump($ret);
if($ret > 100000) {
	$c->set('c1',0);
}