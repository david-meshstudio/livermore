<?php

$c = new SaeCounter();
$ret = $c->get('c1');
var_dump($ret);
if($ret > 100000) {
	$c->set('c1',0);
}