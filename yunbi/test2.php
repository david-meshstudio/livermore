<?php
// $ak = "DQxqaaoFz2IymBEj9N463nvUBMkwXx4bep0zdYva";
// $sk = "yDALXSsQOBWnDYoilqYadJkuhe9x5YaJ35GDIKUR";
// echo microtime(true).',';
// echo hash_hmac('SHA256', 'GET|/api/v2/markets|access_key=xxx&foo=bar&tonce=123456789', 'yyy');

// $mysql = new SaeMysql();
// $sql = "select `rate` from `test_result`;";
// $data = $mysql->runSql($sql);
// $ret = 1;
// foreach ($data as $row) {
// 	$rate = $row['rate'];
// 	$ret = $ret * (1 + $rate / 100);
// }
// echo $ret;
include_once('bootstraps.php');
$ret = API_GetAccountsInfo(ACCESS_KEY, SECRET_KEY);
var_dump($ret);
$strategy = json_decode(STRATEGY,true);
echo $strategy['M1'][0];