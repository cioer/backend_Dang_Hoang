<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once '../../config/captcha.php';
$captcha = new Captcha();
$c = $captcha->generate();
echo json_encode(['captcha_question'=>$c['question'],'captcha_token'=>$c['token']]);
http_response_code(200);
?>
