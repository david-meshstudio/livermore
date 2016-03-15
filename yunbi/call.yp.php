<?php
$url = 'http://livermore.au-syd.mybluemix.net/yp/run.php';

$kv = new SaeKV();
$kv->init();
$yp = $kv->get('yp');
if($yp === 'on') {
	$queue = new SaeTaskQueue('task2');
	$queue->addTask($url);
	$ret = $queue->push();
}