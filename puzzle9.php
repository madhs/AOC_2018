<?php

include_once 'common.funcs.php';

$info = file_get_contents('inputs/input9.txt');

preg_match_all('/^([\d]*).* ([\d]*) points/', $info, $matches, PREG_SET_ORDER);
$player_count = $matches[0][1];
$last_marble = $matches[0][2];

$i = 1;
$players = $player_score = [];
$a = [0];
$pos = 1;
$player_score = array_fill(1, $player_count, 0);

while ($last_marble >= $i) {
	if ($i % 23 == 0) {
		list($pos, $del_val) = array_cycle_delete($a, $pos, -7);
		$player_score[($i % $player_count) + 1] += ($i + $del_val);
	} else {
		$pos = array_cycle_insert($a, $i, $pos, 2);
	}
	$i++;
}

echo PHP_EOL . "High score is: " . max($player_score) . PHP_EOL;