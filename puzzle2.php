<?php

include_once 'common.funcs.php';

$id = file_get_contents('inputs/input2.txt');
$id_list = explode(PHP_EOL, $id);

$twos_count = $threes_count = 0;
foreach ($id_list as $v) {
	$x = array_count_values(str_split($v));
	arsort($x);
	$twos_count += in_array(2, $x) ? 1 : 0;
	$threes_count += in_array(3, $x) ? 1 : 0;
}
echo "Part 1: " . ($twos_count * $threes_count) . PHP_EOL;

sort($id_list);
define('ERROR_MARGIN', 1);
$ec = 0;
$break_all = false;
$answer = null;
for ($i = 0; $i < count($id_list) - 1; $i++) {
	for ($j = 1; $j < count($id_list); $j++) {
		for ($n = 0; $n < strlen($id_list[$i]); $n++) {
			if ($id_list[$i] == $id_list[$j]) {
				continue;
			} elseif (substr($id_list[$i], $n, 1) == substr($id_list[$j], $n, 1) && ERROR_MARGIN >= $ec) {
				$answer .= substr($id_list[$j], $n, 1);
				if ($n == strlen($id_list[$i]) - 1) {
					$break_all = true;
					break;
				}
			} elseif (ERROR_MARGIN > $ec) {
				$ec += 1;
			} else {
				$answer = null;
				$ec = 0;
				break;
			}
		}
		if ($break_all) {break;}
	}
	if ($break_all) {break;}
}
echo "Part 2: " . $answer . PHP_EOL;