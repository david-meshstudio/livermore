<?php
$kv = new SaeKV();
$kv->init();
$sw = $kv->get('sw');
if($sw === 'off') {
	echo 'already off';
	exit();
}

$ret = $kv->set('sw','off');
sleep(2);
$ret = $kv->set('sw','on');

$queue = new SaeTaskQueue('task1');
$queue->addTask("http://livermore.sinaapp.com/yunbi/hit.php");
$ret = $queue->push();
sendMessage(getTimeString().' hit reset');
var_dump($ret);