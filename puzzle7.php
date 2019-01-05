<?php

include_once 'common.funcs.php';

$steps = file_get_contents('inputs/input7.txt');
$steps_arr = explode(PHP_EOL, $steps);

# parent_process_list[child_process] = [process]; child_process_list[proess] = [child_process], all_child_processes; all_parent_processes
$pp_list = $cp_list = $all_cp = $all_pp = [];
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

$available_p = array_diff($all_pp, $all_cp);
$cp_list = array_map(function ($a) {sort($a);return $a;}, $cp_list);

$p_list = array_unique(array_merge($all_cp, $all_pp));
sort($available_p);
$curr_p = min($available_p);
$answer = '';

$i = 0;
do {
	$answer .= $curr_p;
	// $p_list = array_filter($p_list, function ($n) use ($curr_p) {return ($n != $curr_p) ? true : false;});

	array_del_val($curr_p, $p_list);

	$available_p = array_filter($available_p, function ($n) use ($curr_p) {return ($n != $curr_p) ? true : false;});
	$available_p = array_unique(array_merge($available_p, $cp_list[$curr_p]));

	sort($available_p);

	// debug_msg("answer -> $answer");
	// debug_msg("curr -> $curr_p");
	// debug_msg("p_list -> " . implode(', ', $p_list));
	// debug_msg("available -> " . implode(', ', $available_p));
	// debug_msg("parent -> " . @implode(', ', @$pp_list[$curr_p]) . PHP_EOL);

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

echo "Part 1: $answer" . PHP_EOL . PHP_EOL;

$all_p = $p_list = str_split($answer);
$continue = TRUE;

$available_worker = ['v', 'w', 'x', 'y', 'z'];
$occupied_worker = []; // Assoc array
$make_available = []; // Assoc array
$seconds_passed = 0;
$available_p = array_diff($all_pp, $all_cp);

do {

	if (count($p_list) == 0) {
		$seconds_passed--;
		break;
	}

	if (in_array($seconds_passed, array_values($make_available))) {
		foreach ($occupied_worker as $worker_id => $p_id) {
			if ($seconds_passed == $make_available[$worker_id]) {
				array_del_val($p_id, $p_list);

				// Add only those children whose parents are not in the $p_list
				$completed_p = array_diff($all_p, $p_list);
				$callback_f = function ($p) use ($completed_p, $pp_list) {
					foreach ($pp_list[$p] as $pp) {
						if (!in_array($pp, $completed_p)) {
							return false;
						}
					}
					return true;
				};
				$valid_cp_list = array_filter($cp_list[$p_id], $callback_f);
				// var_dump($valid_cp_list);die;

				// $available_p = array_merge($available_p, $cp_list[$p_id]);
				$available_p = array_merge($available_p, $valid_cp_list);
				array_unshift($available_worker, $worker_id);
				unset($occupied_worker[$worker_id]);
				unset($make_available[$worker_id]);
				// debug_msg("[U] Second: $seconds_passed; Worker: $worker_id; PID: $p_id;");
			}
		}
	}

	while (count($available_worker) > 0 && count($available_p) > 0) {
		$p_id = array_shift($available_p);

		$step_time = ord($p_id) - 4;
		$worker_id = array_shift($available_worker);
		$occupied_worker[$worker_id] = $p_id;
		$make_available[$worker_id] = $step_time + $seconds_passed;
		// debug_msg("[O] Second: $seconds_passed; Worker: $worker_id; PID: $p_id; Proc. time: $step_time; Next Available: {$make_available[$worker_id]}");
	}

	$seconds_passed++;
	// debug_msg("$seconds_passed sec. P_LIST: " . count($p_list) . ', Entries: ' . implode(', ', $p_list));
	/*debug_msg(str_pad($seconds_passed, 5, ' ', STR_PAD_LEFT)
		. str_pad(($occupied_worker['v'] ?? '') . " (" . ($make_available['v'] ?? '') . ")", 10, ' ', STR_PAD_LEFT)
		. str_pad(($occupied_worker['w'] ?? '') . " (" . ($make_available['w'] ?? '') . ")", 10, ' ', STR_PAD_LEFT)
		. str_pad(($occupied_worker['x'] ?? '') . " (" . ($make_available['x'] ?? '') . ")", 10, ' ', STR_PAD_LEFT)
		. str_pad(($occupied_worker['y'] ?? '') . " (" . ($make_available['y'] ?? '') . ")", 10, ' ', STR_PAD_LEFT)
		. str_pad(($occupied_worker['z'] ?? '') . " (" . ($make_available['z'] ?? '') . ")", 10, ' ', STR_PAD_LEFT)
	);*/
} while (TRUE);

echo "Seconds passed: $seconds_passed" . PHP_EOL;
