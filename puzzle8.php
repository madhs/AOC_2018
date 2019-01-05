<?php

include_once 'common.funcs.php';

$n = file_get_contents('inputs/input8.txt');
$nos = array_map(function ($i) {return (int) $i;}, explode(' ', $n));

$node_tree = [];
$meta_values = [];

function tree_dive($a, &$meta, &$node_code, $parent_code = 0, $level = 0) {
	global $node_tree, $meta_values;

	$nodes = $a[0];
	$meta_quantity = $a[1];
	$new_a = array_splice($a, 2);
	// debug_msg(str_pad('', $level, '--', STR_PAD_LEFT) . "$nodes : $meta_quantity");
	// debug_msg(str_pad('', $level, '--', STR_PAD_LEFT) . implode(' ', $new_a));

	array_push2($node_tree[$parent_code], $node_code);

	$curr_code = $node_code;
	for ($i = 0; $i < $nodes; $i++) {
		$node_code += 1;
		$new_a = tree_dive($new_a, $meta, $node_code, $curr_code, $level + 1);
	}
	// debug_msg(str_pad('', $level, '--', STR_PAD_LEFT) . implode(' ', $new_a));

	for ($i = 0; $i < $meta_quantity; $i++) {
		array_push2($meta_values[$curr_code], $new_a[$i]);
		$meta += $new_a[$i];
	}
	return array_splice($new_a, $meta_quantity);
}

$total_meta_value = 0;
$node_code = 1;
$x = tree_dive($nos, $total_meta_value, $node_code);

// $meta_sums = array_map('array_sum', $meta_values);

echo "Part 1: $total_meta_value" . PHP_EOL;

// print_r($meta_values);
// print_r($node_tree);die;

function calc_node_value($root) {
	global $node_tree, $meta_values;
	if (isset($node_tree[$root])) {
		$node_value = 0;
		if (isset($meta_values[$root])) {
			for ($i = 0; $i < count($meta_values[$root]); $i++) {
				$meta_idx = $meta_values[$root][$i] - 1;
				if (isset($node_tree[$root][$meta_idx])) {
					$nv = calc_node_value($node_tree[$root][$meta_idx]);
					$node_value += $nv;
				}
			}
		}
		if ($node_value == 0) {
			debug_msg($root);
		}
		return $node_value;
	} else {
		return array_sum($meta_values[$root]);
	}
}

$root_node_value = calc_node_value(1);

echo "Part 2: " . $root_node_value . PHP_EOL;
