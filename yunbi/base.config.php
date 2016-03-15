<?php
define('ACCESS_KEY','DQxqaaoFz2IymBEj9N463nvUBMkwXx4bep0zdYva');
define('SECRET_KEY','yDALXSsQOBWnDYoilqYadJkuhe9x5YaJ35GDIKUR');

define('STRATEGY','{"M1":["eb","bc","ce"],"M2":["ec","cb","be"],"M3":["bc","ce","eb"],"M4":["be","ec","cb"],"M5":["ce","eb","bc"],"M6":["cb","be","ec"]}');

function getMarketSide($str) {
	$map = array();
	$map['eb'] = array('ethbtc','sell');
	$map['be'] = array('ethbtc','buy');
	$map['bc'] = array('btccny','sell');
	$map['cb'] = array('btccny','buy');
	$map['ec'] = array('ethcny','sell');
	$map['ce'] = array('ethcny','buy');
	return $map[$str];
}