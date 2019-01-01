<?php

include_once 'common.funcs.php';

$orig_polymer_string = $polymer_string = file_get_contents('inputs/input5.txt');

$orig_str_len = $curr_str_len = strlen($polymer_string);
$shortest_polymer = 99999999999999999999;

for ($h = 0; $h < 26; $h++) {
	$polymer_string = $orig_polymer_string;

	$polymer_string = str_replace(chr(65 + $h), '', $polymer_string);
	$polymer_string = str_replace(chr(97 + $h), '', $polymer_string);

	do {
		$prev_major_str_len = strlen($polymer_string);
		for ($i = 0; $i < 26; $i++) {
			do {
				$prev_str_len = $curr_str_len;

				$flip = chr(65 + $i) . chr(97 + $i);
				$flop = chr(97 + $i) . chr(65 + $i);

				$polymer_string = preg_replace("/$flip/m", '', $polymer_string);
				$polymer_string = preg_replace("/$flop/m", '', $polymer_string);

				$curr_str_len = strlen($polymer_string);
				$continue_reaction = ($prev_str_len != $curr_str_len) ? true : false;

			} while ($continue_reaction);
		}
		$curr_major_str_len = strlen($polymer_string);
	} while ($curr_major_str_len != $prev_major_str_len);

	$shortest_polymer = ($curr_major_str_len < $shortest_polymer) ? $curr_major_str_len : $shortest_polymer;
}

echo "Shortest polymer length $shortest_polymer" . PHP_EOL;
