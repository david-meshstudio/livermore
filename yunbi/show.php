<?php
$mysql = new SaeMysql();
$sql = "select * from `test_result`;";
$ret = $mysql->getData($sql);
foreach ($ret as $row) {
	echo $row['rate'].','.$row['createtime'].'<br>';
}
$sql = "select * from `test_result2`;";
$ret = $mysql->getData($sql);
foreach ($ret as $row) {
	echo $row['rate'].','.$row['createtime'].'<br>';
}