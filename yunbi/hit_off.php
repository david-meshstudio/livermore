<?php
$kv = new SaeKV();
$kv->init();
$sw = $kv->get('sw');
if($sw === 'off') {
	echo 'already off';
	exit();
}

$ret = $kv->set('sw','off');
var_dump($ret);
$sw = $kv->get('sw');
var_dump($sw);