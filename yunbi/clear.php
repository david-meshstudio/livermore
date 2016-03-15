<?php
include_once('bootstraps.php');

$ret = API_ClearOrder(ACCESS_KEY, SECRET_KEY);
echo $ret;