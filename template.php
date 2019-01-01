<?php

include_once 'common.funcs.php';

$freq = file_get_contents('inputs/input1.txt');
$freq_list = explode(PHP_EOL, $freq);
