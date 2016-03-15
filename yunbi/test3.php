<?php
include_once('bootstraps.php');
$accounts = API_GetAccountsInfo(ACCESS_KEY, SECRET_KEY);
// $market = API_GetMarketPrice();
// $benifits = getMethodBenifits($market,$accounts);
// var_dump($accounts['cny']);
// var_dump($accounts['btc']);
// var_dump($accounts['eth']);
echo 'eth='.$accounts['eth'][0].', btc='.$accounts['btc'][0].', cny='.$accounts['cny'][0];
echo '<br>';
echo 'eth='.$accounts['eth'][1].', btc='.$accounts['btc'][1].', cny='.$accounts['cny'][1];

// sendMessage('test');