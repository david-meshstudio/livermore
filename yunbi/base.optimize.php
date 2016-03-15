<?php
function getMarketAnalysis($direct,$market,$input) {
	switch (strtolower($direct)) {
		case 'eb':
			$rate = $market[0]['bids'][0]['price'];
			if($rate == 0) break;
			$total = $market[0]['bids'][0]['volume'];
			$output = min($input,$total) * $rate;
			$remain = $input - min($input,$total);
			break;
		case 'be':
			$rate = $market[0]['asks'][0]['price'];
			if($rate == 0) break;
			$total = $market[0]['asks'][0]['volume'];
			$output = min($input,$total * $rate) / $rate;
			$remain = $input - min($input,$total * $rate);
			break;
		case 'bc':
			$rate = $market[1]['bids'][0]['price'];
			if($rate == 0) break;
			$total = $market[1]['bids'][0]['volume'];
			$output = min($input,$total) * $rate;
			$remain = $input - min($input,$total);
			break;
		case 'cb':
			$rate = $market[1]['asks'][0]['price'];
			if($rate == 0) break;
			$total = $market[1]['asks'][0]['volume'];
			$output = min($input,$total * $rate) / $rate;
			$remain = $input - min($input,$total * $rate);
			break;
		case 'ec':
			$rate = $market[2]['bids'][0]['price'];
			if($rate == 0) break;
			$total = $market[2]['bids'][0]['volume'];
			$output = min($input,$total) * $rate;
			$remain = $input - min($input,$total);
			break;
		case 'ce':
			$rate = $market[2]['asks'][0]['price'];
			if($rate == 0) break;
			$total = $market[2]['asks'][0]['volume'];
			$output = min($input,$total * $rate) / $rate;
			$remain = $input - min($input,$total * $rate);
			break;
		default:
			$rate = 1;
			$total = 0;
			$output = $input;
			break;
	}
	return array($rate,$total,$output,$remain);
}

function getMethodBenifitsFix($market,$amount) {
	$benifits = array();
	$benifits['M1'] = getMainMethodBenifit('M1','eth',$market,$amount);
	$benifits['M2'] = getMainMethodBenifit('M2','eth',$market,$amount);
	return $benifits;
}

function getMethodBenifitsFix3($market,$amount1,$amount2,$amount3) {
	$benifits = array();
	$benifits['M1'] = getMainMethodBenifit('M1','eth',$market,$amount1);
	$benifits['M2'] = getMainMethodBenifit('M2','eth',$market,$amount1);
	$benifits['M3'] = getMainMethodBenifit('M3','btc',$market,$amount2);
	$benifits['M4'] = getMainMethodBenifit('M4','btc',$market,$amount2);
	$benifits['M5'] = getMainMethodBenifit('M5','cny',$market,$amount3);
	$benifits['M6'] = getMainMethodBenifit('M6','cny',$market,$amount3);
	return $benifits;
}

function getMethodBenifits($market,$accounts) {
	$benifits = array();
	$benifits['M1'] = getMainMethodBenifit('M1','eth',$market,$accounts[$resource][0]);
	$benifits['M2'] = getMainMethodBenifit('M2','eth',$market,$accounts[$resource][0]);
	// $benifits['M3'] = getMainMethodBenifit('M3','btc',$market,$accounts);
	// $benifits['M4'] = getMainMethodBenifit('M4','btc',$market,$accounts);
	// $benifits['M5'] = getMainMethodBenifit('M5','cny',$market,$accounts);
	// $benifits['M6'] = getMainMethodBenifit('M6','cny',$market,$accounts);
	return $benifits;
}

function getMainMethodBenifit($name,$resource,$market,$amount) {
	$strategy = json_decode(STRATEGY, true);
	$benifit = array('feasible'=>true,'good'=>false,'detail'=>array(),'rate'=>0);
	$input = $amount;
	$orders = array();
	$benifit['orders'] = $orders;
	$remain0 = 0;
	foreach ($strategy[$name] as $item) {
		list($rate,$total,$output,$remain) = getMarketAnalysis($item,$market,$input);
		if($rate == 0) break;
		if($input != $amount && $remain > 0) {
			$benifit['feasible'] = false;
		} else if($input == $amount) {
			$remain0 = $remain;
		}
		$benifit['detail'][] = array($rate,$total,$output,$remain);
		list($marketstr,$side) = getMarketSide($item);
		$orders[] = genOrderPara($marketstr,$side,$side === 'buy' ? floor(($input - $remain) / $rate * 10000) / 10000 : ($input - $remain),$rate);
		$input = floor($output * 10000) / 10000;
	}
	if($benifit['feasible'] && $amount > 0) {
		$benifit['rate'] = ($output + $remain0 - $amount) / $amount * 100;
	}
	if($benifit['feasible'] && $benifit['rate'] > 0) {
		$benifit['good'] = true;
		$benifit['orders'] = $orders;
	}
	return $benifit;
}

function getFishBenifitsFix($market,$amount) {
	$gaps = array();
	$gaps[] = array($market[0]['bids'][0]['price'],$market[0]['asks'][0]['price'],($market[0]['asks'][0]['price']-$market[0]['bids'][0]['price'])/$market[0]['bids'][0]['price']*100);
	$gaps[] = array($market[1]['bids'][0]['price'],$market[1]['asks'][0]['price'],($market[1]['asks'][0]['price']-$market[1]['bids'][0]['price'])/$market[1]['bids'][0]['price']*100);
	$gaps[] = array($market[2]['bids'][0]['price'],$market[2]['asks'][0]['price'],($market[2]['asks'][0]['price']-$market[2]['bids'][0]['price'])/$market[2]['bids'][0]['price']*100);
	echo json_encode($gaps);
	echo '<br><br><br>';
	$benifits = array();
	$mbk = $market;
	$market[1]['bids'][0]['price'] = $gaps[1][1] * 0.99;
	$market[1]['asks'][0]['price'] = $gaps[1][0] * 1.01;
	$benifits['M1'] = getFishMethodBenifit('M1','eth',$market,$amount);
	$benifits['M2'] = getFishMethodBenifit('M2','eth',$market,$amount);
	$market = $mbk;
	$market[2]['bids'][0]['price'] = $gaps[2][1] * 0.99;
	$market[2]['asks'][0]['price'] = $gaps[2][0] * 1.01;
	$benifits['M3'] = getFishMethodBenifit('M1','eth',$market,$amount);
	$benifits['M4'] = getFishMethodBenifit('M2','eth',$market,$amount);
	$market = $mbk;
	$market[0]['bids'][0]['price'] = $gaps[0][1] * 0.99;
	$market[0]['asks'][0]['price'] = $gaps[0][0] * 1.01;
	$benifits['M5'] = getFishMethodBenifit('M1','eth',$market,$amount);
	$benifits['M6'] = getFishMethodBenifit('M2','eth',$market,$amount);
	return $benifits;
}

function getFishMethodBenifit($name,$resource,$market,$amount) {
	$strategy = json_decode(STRATEGY, true);
	$benifit = array('feasible'=>true,'good'=>false,'detail'=>array(),'rate'=>0);
	$input = $amount;
	$orders = array();
	$benifit['orders'] = $orders;
	$remain0 = 0;
	foreach ($strategy[$name] as $item) {
		list($rate,$total,$output,$remain) = getMarketAnalysis($item,$market,$input);
		if($rate == 0) break;
		if($input != $amount && $remain > 0) {
			$benifit['feasible'] = false;
		} else if($input == $amount) {
			$remain0 = $remain;
		}
		$benifit['detail'][] = array($rate,$total,$output,$remain);
		list($marketstr,$side) = getMarketSide($item);
		$orders[] = genOrderPara($marketstr,$side,$side === 'buy' ? floor(($input - $remain) / $rate * 10000) / 10000 : ($input - $remain),$rate);
		$input = floor($output * 10000) / 10000;
	}
	if($benifit['feasible'] && $amount > 0) {
		$benifit['rate'] = ($output + $remain0 - $amount) / $amount * 100;
	}
	if($benifit['feasible'] && $benifit['rate'] > 0) {
		$benifit['good'] = true;
		$benifit['orders'] = $orders;
	}
	return $benifit;
}

function genOrderPara($market,$side,$volume,$price) {
	$para = array();
	$para['market'] = $market;
	$para['side'] = $side;
	$para['volume'] = $volume;
	$para['price'] = $price;
	return $para;
}