<?php
include_once('bootstraps.php');
$purl = "https://poloniex.com/public?command=returnOrderBook&currencyPair=BTC_ETH&depth=1";
$pret = file_get_contents($purl);
$pinfo = json_decode($pret, true);
if($pinfo['asks'] === null) exit();

$yurl = "https://yunbi.com/api/v2/order_book.json?market=ethbtc&asks_limit=1&bids_limit=1";
$yret = file_get_contents($yurl);
$yinfo = json_decode($yret, true);
if($yinfo['asks'] === null) exit();

$pap1 = $pinfo['asks'][0][0];
$pac1 = $pinfo['asks'][0][1];
$pbp1 = $pinfo['bids'][0][0];
$pbc1 = $pinfo['bids'][0][1];

$yap1 = $yinfo['asks'][0]['price'];
$yac1 = $yinfo['asks'][0]['volume'];
$ybp1 = $yinfo['bids'][0]['price'];
$ybc1 = $yinfo['bids'][0]['volume'];

$prof1 = min($pac1, $ybc1) * ($pap1 / $ybp1 - 1);// btc base, P sell eth, Y buy eth
$prof2 = min($pbc1, $yac1) * ($yap1 / $pbp1 - 1);// btc base, Y sell eth, P buy eth

echo $prof1.','.$prof2;