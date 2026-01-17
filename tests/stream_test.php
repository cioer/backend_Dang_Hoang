<?php
$url = "http://localhost/Backend/api/banners/stream.php";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$out = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);
if ($err) {
  echo "STREAM_TEST: ERROR $err\n";
  exit(1);
}
if (strpos($out, "event: banners") === false && strpos($out, "heartbeat") === false) {
  echo "STREAM_TEST: FAIL no banners or heartbeat\n";
  exit(2);
}
echo "STREAM_TEST: OK\n";
