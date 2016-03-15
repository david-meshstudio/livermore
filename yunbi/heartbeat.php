<?php
include_once('bootstraps.php');

$kv = new SaeKV();
$kv->init();
$last = $kv->get('last');
$lastf = $kv->get('lastf');

$c = new SaeCounter();
$current = $c->get('c1');
$currentf = $c->get('c2');

if($current === $last) {
	$ret = file_get_contents("http://livermore.sinaapp.com/yunbi/hit_reset.php");
	echo 'reset';
	// sendMessage(getTimeString().' hit reset');
} else {
	if($current > 100000) $current = 0;
	$ret = $kv->set('last', $current);
	echo 'ok';
}

if($currentf === $lastf) {
	$ret = file_get_contents("http://livermore.sinaapp.com/yunbi/fish_reset.php");
	echo 'reset';
	// sendMessage(getTimeString().' fish reset');
} else {
	if($currentf > 100000) $currentf = 0;
	$ret = $kv->set('lastf', $currentf);
	echo 'ok';
}