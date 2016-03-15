<?php
$kv = new SaeKV();
$kv->init();
$sw = $kv->get('fsw');
if($sw === 'off') {
	echo 'already off';
	exit();
}

$ret = $kv->set('fsw','off');
var_dump($ret);
$sw = $kv->get('fsw');
var_dump($sw);