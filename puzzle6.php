<?php

include_once 'common.funcs.php';

$coordinates = file_get_contents('inputs/input6.txt');
$coordinates_array = explode(PHP_EOL, $coordinates);
$coordinate_list = [];

$max_x = $max_y = 0;
foreach ($coordinates_array as $l) {
	preg_match_all('/(\d*), (\d*)/m', $l, $matches, PREG_SET_ORDER);

	$x = (int) $matches[0][1];
	$y = (int) $matches[0][2];
	$coordinate_list[] = [$x, $y];
	$max_x = ($max_x < $x) ? $x : $max_x;
	$max_y = ($max_y < $y) ? $y : $max_y;
}
$grid = null;
set_matrix($grid, $max_x + 1, $max_y + 1, '');

$count = 1;
$curr_x = $curr_y = 0;
$max_points = ($max_x + 1) * ($max_y + 1);

do {

	$nearest_dist = $new_near_dist = 999999999999999999;
	foreach ($coordinate_list as $k => $c) {
		$dist = distance_between($curr_x, $curr_y, $c[0], $c[1]);
		$new_near_dist = ($dist < $nearest_dist) ? $dist : ($dist > $nearest_dist ? $nearest_dist : 0);

		if ($curr_x == $c[0] && $curr_y == $c[1]) {
			matrix_replace($grid, $curr_x, $curr_y, $k);
			break;
		} elseif ($new_near_dist == 0) {
			matrix_replace($grid, $curr_x, $curr_y, '.');
			// break;
		} elseif ($nearest_dist != $new_near_dist) {
			$nearest_dist = $new_near_dist;
			matrix_replace($grid, $curr_x, $curr_y, $k);
		}
	}

	// echo "$curr_x, $curr_y" . PHP_EOL;
	$curr_y += ($curr_x == $max_x) ? 1 : 0;
	$curr_x += ($curr_x == $max_x) ? (-1 * $curr_x) : 1;
	$count += 1;
} while ($count <= $max_points);

$left_edge = $right_edge = $top_edge = $bottom_edge = [];
foreach ($grid as $row) {
	$left_edge[] = $row[0];
	$right_edge[] = $row[$max_x];
	foreach ($row as $c) {
		if (!isset($counter[$c])) {
			$counter[$c] = 1;
		} else {
			$counter[$c] += 1;
		}
	}
}

/*$fh = fopen('grid6.txt', 'w');
foreach ($grid as $row) {
$str = '';
foreach ($row as $c) {
$str .= str_pad($c, 3, ' ');
}
fwrite($fh, $str . PHP_EOL);
}
fclose($fh);*/
$top_edge = $grid[0];
$bottom_edge = $grid[$max_y];

$outliers = array_unique(array_merge(array_unique($top_edge), array_unique($bottom_edge), array_unique($left_edge), array_unique($right_edge)));
// print_r($outliers);

foreach ($outliers as $o) {
	if ($counter[$o]) {
		unset($counter[$o]);
	}
}

echo "Maximum area: " . max($counter) . PHP_EOL;

$safe_area_count = 0;
for ($j = 0; $j < $max_y; $j++) {
	for ($i = 0; $i < $max_x; $i++) {
		$safe = true;
		$sum_dist = 0;
		foreach ($coordinate_list as $co) {
			$dist = distance_between($i, $j, $co[0], $co[1]);

			$sum_dist += $dist;
			if ($sum_dist >= 10000) {
				$safe = false;
				break;
			}
		}
		$safe_area_count += $safe ? 1 : 0;
	}
}

echo "Safe area: $safe_area_count" . PHP_EOL;
