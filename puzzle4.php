<?php

include_once 'common.funcs.php';

$log_sheet = file_get_contents('inputs/input4.txt');
$log_sheet = explode(PHP_EOL, $log_sheet);

// Guard ID
// date, time of asleep and up
// calc minutes of sleep
// calc array of times slept for ID

$total_hrs_slept = [];
foreach ($log_sheet as $v) {
	$re = '/\[(.*) (.*)\] (\w*) (\S*)/m';
	preg_match_all($re, $v, $matches, PREG_SET_ORDER, 0);

	$log_date = $matches[0][1];
	$log_time = $matches[0][2];
	$log_action = $matches[0][3];
	$log_guard_id = $matches[0][4];

	if ($log_action == 'Guard') {
		$curr_guard_id = $log_guard_id;
		$sleep_start_datetime = $sleep_end_datetime = null;
	} elseif ($log_action == 'falls') {
		$sleep_start_datetime = "$log_date $log_time";
	} elseif ($log_action == 'wakes') {
		$sleep_end_datetime = "$log_date $log_time";

		$start = new DateTime($sleep_start_datetime);
		$end = new DateTime($sleep_end_datetime);

		$interval = $end->diff($start);

		$interval_in_min = $interval->format("%D") * 24 * 60;
		$interval_in_min += $interval->format("%h") * 60;
		$interval_in_min += $interval->format("%i");

		if (!isset($total_hrs_slept[$curr_guard_id])) {
			$total_hrs_slept[$curr_guard_id] = $interval_in_min;
		} else {
			$total_hrs_slept[$curr_guard_id] += $interval_in_min;
		}

		// $re = '/.* (.*):(.*)/m';
		// preg_match_all($re, $sleep_start_datetime, $m1, PREG_SET_ORDER, 0);
		// preg_match_all($re, $sleep_end_datetime, $m2, PREG_SET_ORDER, 0);

		for ($i = 0; $i < $interval_in_min; $i++) {
			$key = $start->format('H:i');
			if (!isset($sleep_tracking[$curr_guard_id][$key])) {
				if (!isset($sleep_tracking[$curr_guard_id])) {
					$sleep_tracking[$curr_guard_id] = [];
				}

				$sleep_tracking[$curr_guard_id][$key] = 1;
			} else {
				$sleep_tracking[$curr_guard_id][$key] += 1;
			}
			$start->add(new DateInterval('PT1M'));
		}
		// print_r($sleep_tracking);
		// die;
	}
}

arsort($total_hrs_slept);
foreach ($total_hrs_slept as $guard_id => $v) {
	$max_sleeper = $guard_id;
	break;
}

// print_r($max_sleeper . PHP_EOL);
// print_r($sleep_tracking[$max_sleeper]);

$max_value = 0;
foreach ($sleep_tracking as $guard_id => $freq) {
	foreach ($freq as $t => $v) {
		if ($v > $max_value) {
			$max_value = $v;
			$most_slept_time = $t;
			$most_freq_slept_guard = $guard_id;
		}
	}
}

print_r($most_freq_slept_guard . PHP_EOL);
print_r($most_slept_time);
