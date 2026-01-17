<?php
$url = "http://localhost/Backend/api/banners/get_active.php";
$json = @file_get_contents($url);
if ($json === false) { echo "GET_ACTIVE_TEST: ERROR cannot fetch\n"; exit(1); }
$arr = json_decode($json, true);
if (!is_array($arr)) { echo "GET_ACTIVE_TEST: FAIL not array\n"; exit(2); }
echo "GET_ACTIVE_TEST: OK count=" . count($arr) . "\n";
