<?php
$url = 'http://livermore.sinaapp.com/yunbi/rehit.php?order=';
$order = $_GET['order'];

$queue = new SaeTaskQueue('task1');
$queue->addTask($url.$order);
$ret = $queue->push();