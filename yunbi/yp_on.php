<?php
$kv = new SaeKV();
$kv->init();
$yp = $kv->get('yp');
if($yp === 'on') {
	echo 'already on';
	exit();
}
$ret = $kv->set('yp','on');
var_dump($ret);
$yp = $kv->get('yp');
var_dump($yp);