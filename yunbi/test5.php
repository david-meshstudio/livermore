<?php
include_once('bootstraps.php');
$id = '96033805';
$trade = API_GetOrder($id,ACCESS_KEY,SECRET_KEY);
var_dump($trade);

echo '<br>';
echo time();
echo '<br>';
echo getMillisecond();
echo '<br>';
echo getTimeString();
