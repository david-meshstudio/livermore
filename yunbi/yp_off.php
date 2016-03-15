<?php
$kv = new SaeKV();
$kv->init();
$yp = $kv->get('yp');
if($yp === 'off') {
	echo 'already off';
	exit();
}

$ret = $kv->set('yp','off');
var_dump($ret);
$yp = $kv->get('yp');
var_dump($yp);