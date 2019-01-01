<?php

include_once 'common.funcs.php';

$freq = file_get_contents('inputs/input1.txt');
$freq_list = explode(PHP_EOL, $freq);

echo "Final freq: " . array_sum($freq_list) . PHP_EOL;

$ok = true;
$sum = $i = 0;
$known_freq_sums = [];
do {
	$sum += $freq_list[$i];
	if (isset($known_freq_sums[$sum])) {
		$ok = false;
		echo "Part 2 freq: " . $sum . PHP_EOL;
	} else {
		$known_freq_sums[$sum] = 1;
	}
	$i += (count($freq_list) == ($i + 1)) ? (-1 * $i) : 1;
} while ($ok);
