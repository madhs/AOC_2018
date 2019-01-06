<?php

define('DEBUG_MODE', TRUE);

function substr_map(&$line, $callback, $start, $length) {
	$end_pos = strlen($line) - 1;

	$str_start_pos = ($start < 0) ? $end_pos + $start : $start;

	$str_curr_pos = $str_start_pos;
	$str_length = (($end_pos - $str_start_pos + 1) < $length) ? ($end_pos - $str_start_pos + 1) : $length;

	for ($i = 0; $i < $str_length; $i++) {
		$v = substr($line, $str_curr_pos, 1);
		$line = substr_replace($line, $callback($str_curr_pos, $v), $str_curr_pos, 1);
		$str_curr_pos++;
	}
}

function set_str_matrix(&$grid, $x_len, $y_len, $val = ' ') {
	$grid = [];
	for ($i = 0; $i < $y_len; $i++) {
		$grid[] = str_pad('', $x_len, $val, STR_PAD_RIGHT);
	}
}

function str_matrix_replace(&$grid, $x, $y, $val) {
	$c = function ($i, $v) use ($val) {
		return $val;
	};
	substr_map($grid[$y], $c, $x, 1);
}

function set_matrix(&$grid, $x_len, $y_len, $val = ' ') {
	$grid = [];

	for ($j = 0; $j < $y_len; $j++) {
		$grid[$j] = [];
		for ($i = 0; $i < $x_len; $i++) {
			$grid[$j][$i] = $val;
		}
	}
}

function matrix_replace(&$grid, $x, $y, $val) {
	$grid[$y][$x] = $val;
}

function distance_between($x1, $y1, $x2, $y2) {
	return abs($x1 - $x2) + abs($y1 - $y2);
}

function debug_msg($s) {
	echo DEBUG_MODE ? $s . PHP_EOL : '';
}

function array_del_val($v, &$a) {
	if (false !== ($pos = array_search($v, $a))) {
		unset($a[$pos]);
		return true;
	}

	return false;
}

function array_push2(&$a, $v) {
	$a = array_merge(($a ?? []), [$v]);
}

class show_memory_timing {
	private $st;

	function __construct() {
		$this->st = microtime(true);
	}

	function __destruct() {
		echo PHP_EOL . "=====================" . PHP_EOL;
		$et = microtime(true) - $this->st;
		echo round($et, 5) . ' s' . PHP_EOL;
		echo $this->memory_used() . PHP_EOL;
	}

	function memory_used() {
		$mem_used = memory_get_usage();
		if ($mem_used < 1024) {
			return $mem_used . " B";
		} elseif ($mem_used >= 1024 and $mem_used < pow(1024, 2)) {
			return ($mem_used / 1024) . " KB";
		} elseif ($mem_used >= pow(1024, 2) and $mem_used < pow(1024, 3)) {
			return ($mem_used / pow(1024, 2)) . " MB";
		} elseif ($mem_used >= pow(1024, 3) and $mem_used < pow(1024, 4)) {
			return ($mem_used / pow(1024, 3)) . " GB";
		}
	}

}

function array_insert(&$a, $v, $pos) {
	$tmp = array_splice($a, $pos);
	// echo $pos . ' ';
	// echo implode(',', $a) . ' ';
	// echo implode(',', $tmp);die;
	array_push2($a, $v);
	$a = array_merge($a, $tmp);
}

function array_cycle_insert(&$a, $v, $curr_pos, $offset) {
	$c = count($a);
	$offset = ($offset < 0) ? ($offset + 1) : $offset;
	$ins_idx = ($curr_pos - 1) + $offset; // Convert position to array idx
	while ($ins_idx > $c || $ins_idx < 0) {
		$ins_idx += ($ins_idx > $c) ? -$c : $c;
	}
	array_insert($a, $v, $ins_idx);
	return $ins_idx + 1;
}

function array_delete(&$a, $pos) {
	$tmp = array_splice($a, $pos);
	// echo $pos . ' ';
	// echo implode(',', $a) . ' ';
	// echo implode(',', $tmp) . ' ';
	$v = array_shift($tmp);
	$a = array_merge($a, $tmp);
	// echo implode(',', $a);die;
	return $v;
}

function array_cycle_delete(&$a, $curr_pos, $offset) {
	$c = count($a);
	// $offset = ($offset < 0) ? ($offset + 1) : $offset;
	$del_idx = ($curr_pos - 1) + $offset; // Convert position to array idx
	while ($del_idx > $c || $del_idx < 0) {
		$del_idx += ($del_idx > $c) ? -$c : $c;
	}
	$v = array_delete($a, $del_idx);
	return [($del_idx + 1), $v];
}

global $TIMER;
$TIMER = new show_memory_timing;
// echo chr(65) . ord('A');

define('TEST_CYCLE_ARRAY_FUNCS', 0);

if (TEST_CYCLE_ARRAY_FUNCS) {
	$a = [1, 2, 3, 4, 5, 6];
	debug_msg("Input: " . implode(',', $a));

	$idx = array_cycle_insert($a, 'A', 2, 1);
	debug_msg(implode(',', $a) . "; Ins val: {$a[$idx]}; Array pos: $idx");
	list($idx, $v) = array_cycle_delete($a, $idx + 1, -3);
	debug_msg(implode(',', $a) . "; Del val: $v; Array pos: $idx");
}