<?php

include_once 'common.funcs.php';

$f = file_get_contents('inputs/input3.txt');
$area_claim = explode(PHP_EOL, $f);

$grid = [];
set_str_matrix($grid, 1000, 1000, '0');
foreach ($area_claim as $str) {
	$re = '/(\w*) @ (\d*),(\d*): (\d*)x(\d*)/m';

	preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

	// echo str_pad(str_pad($matches[0][0], 18, ' ', STR_PAD_LEFT), 20, ' ', STR_PAD_RIGHT) . " ";
	$c = function ($i, $v) {
		$v = (int) $v;
		$v += 1;
		return $v;
	};

	for ($i = 0; $i < $matches[0][5]; $i++) {
		substr_map($grid[$matches[0][3] + $i], $c, $matches[0][2], $matches[0][4]);
		// echo ".";
	}
	// echo PHP_EOL;
}
echo PHP_EOL;

$overlap_cnt = 0;
// $fh = fopen('grid3.txt', 'w');
foreach ($grid as $r) {
	// fwrite($fh, $r . PHP_EOL, strlen($r . PHP_EOL));
	$r = str_replace('1', '', $r);
	$r = str_replace('0', '', $r);
	$overlap_cnt += strlen($r);
}
// fclose($fh);
echo "Answer: $overlap_cnt" . PHP_EOL;

foreach ($area_claim as $str) {
	$re = '/(\w*) @ (\d*),(\d*): (\d*)x(\d*)/m';

	preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

	$x_offset = $matches[0][2];
	$y_offset = $matches[0][3];
	$x = $matches[0][4];
	$y = $matches[0][5];

	for ($i = 0; $i < $y; $i++) {
		$grid_str = substr($grid[$y_offset + $i], $x_offset, $x);
		$grid_str = str_replace('1', '', $grid_str);
		if ($grid_str == '') {
			$all_good = true;
		} else {
			if (isset($all_good)) {
				unset($all_good);
			}
			break;
		}
	}

	if (isset($all_good)) {
		echo "The one with clean grid: {$matches[0][0]}" . PHP_EOL;
		unset($all_good);
	}
}