<?php
$kv = new SaeKV();
$kv->init();
$sw = $kv->get('sw');
if($sw === 'on') {
	echo 'already on';
	exit();
}
$ret = $kv->set('sw','on');
var_dump($ret);
$sw = $kv->get('sw');
var_dump($sw);

$queue = new SaeTaskQueue('task1');
$queue->addTask("http://livermore.sinaapp.com/yunbi/hit.php");
$ret = $queue->push();
var_dump($ret);