<?php
include_once('bootstraps.php');
$mysql = new SaeMysql();

$market = API_GetMarketPrice();
$benifits = getFishBenifitsFix($market,1);

foreach ($benifits as $key => $value) {
	if($value['good'] && $value['rate'] > 1) {
		if(!in_array($key, array('M5','M6'))) {
			sendMessage(getTimeString().' fish found '.$key.', '.$value['rate']);
		}
		$sql = "insert into `fish_result` (rate,remark) values ('".$value['rate']."','".$key."=".json_encode($value)."');";
		// echo $sql.'<br><br>';
		$ret = $mysql->runSql($sql);
		// var_dump($ret);
	}
}
echo json_encode($benifits);
// sleep(1);
// $queue = new SaeTaskQueue('task2');
// $queue->addTask("http://livermore.sinaapp.com/yunbi/check_fish.php");
// $ret = $queue->push();