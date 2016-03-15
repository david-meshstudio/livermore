<?php
include_once('bootstraps.php');
$kv = new SaeKV();
$kv->init();
$sw = $kv->get('fsw');
if($sw === 'off') {
	echo 'already off';
	exit();
}

$ret = $kv->set('fsw','off');
sleep(2);
$ret = $kv->set('fsw','on');

$queue = new SaeTaskQueue('task2');
$queue->addTask("http://livermore.sinaapp.com/yunbi/fish.php");
$ret = $queue->push();
sendMessage(getTimeString().' fish reset');
var_dump($ret);