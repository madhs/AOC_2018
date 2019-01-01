<?php

include_once 'common.funcs.php';

$steps = file_get_contents('inputs/input7.txt');
$steps_arr = explode(PHP_EOL, $steps);

$pp_list = $cp_list = $p_list = $all_cp = $all_pp = [];
foreach ($steps_arr as $l) {
	preg_match_all('/Step (\w*) must be finished before step (\w*) can begin/m', $l, $matches, PREG_SET_ORDER);
	$pp = $matches[0][1];
	$p = $matches[0][2];

	if (!isset($pp_list[$p])) {
		$pp_list[$p] = [];
	}

	$pp_list[$p][] = $pp;

	if (!isset($cp_list[$p])) {
		$cp_list[$p] = [];
	}

	$cp_list[$pp][] = $p;

	if (!in_array($p, $all_cp)) {
		$all_cp[] = $p;
	}

	if (!in_array($pp, $all_pp)) {
		$all_pp[] = $pp;
	}

}

$cp_count = array_map(function ($a) {return count($a);}, $cp_list);
$cp_visits = array_map(function ($a) {return 0;}, $cp_list);

// B,F,G
$available_p = array_diff($all_pp, $all_cp);
$cp_list = array_map(function ($a) {sort($a);return $a;}, $cp_list);

$p_list = array_unique(array_merge($all_cp, $all_pp));
sort($available_p);
$curr_p = min($available_p);
$completed_p = [];
$answer = '';

$i = 0;
do {
	$answer .= $curr_p;
	$p_list = array_filter($p_list, function ($n) use ($curr_p) {return ($n != $curr_p) ? true : false;});

	$available_p = array_filter($available_p, function ($n) use ($curr_p) {return ($n != $curr_p) ? true : false;});
	$available_p = array_unique(array_merge($available_p, $cp_list[$curr_p]));

	sort($available_p);

	if (0) {
		echo "answer -> $answer" . PHP_EOL;
		echo "curr -> $curr_p" . PHP_EOL;
		echo "p_list -> " . implode(', ', $p_list) . PHP_EOL;
		echo "available -> " . implode(', ', $available_p) . PHP_EOL;
		echo "parent -> " . @implode(', ', @$pp_list[$curr_p]) . PHP_EOL . PHP_EOL;
	}

	foreach ($available_p as $v) {
		// echo "    looping -> $v" . PHP_EOL;
		if (isset($pp_list[$v])) {
			$ok = false;
			foreach ($pp_list[$v] as $parent_p) {
				if (!in_array($parent_p, $p_list)) {
					$ok = true;
				} else {
					$ok = false;
					break;
				}
			}
			if ($ok) {
				$curr_p = $v;
				break;
			}
		} else {
			$curr_p = $v;
			break;
		}
	}
	$i++;
} while (count($available_p) > 0);

echo $answer . PHP_EOL;
// echo implode('', array_unique(array_merge($all_cp, $all_pp))) . PHP_EOL;
