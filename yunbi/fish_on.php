<?php
$kv = new SaeKV();
$kv->init();
$sw = $kv->get('fsw');
if($sw === 'on') {
	echo 'already on';
	exit();
}
$ret = $kv->set('fsw','on');
var_dump($ret);
$sw = $kv->get('fsw');
var_dump($sw);

$queue = new SaeTaskQueue('task2');
$queue->addTask("http://livermore.sinaapp.com/yunbi/fish.php");
$ret = $queue->push();
var_dump($ret);