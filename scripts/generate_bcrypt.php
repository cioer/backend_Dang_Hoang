<?php
header("Content-Type: application/json; charset=UTF-8");
$pwd = isset($_GET['password']) ? (string)$_GET['password'] : '123456';
$hash = password_hash($pwd, PASSWORD_BCRYPT);
echo json_encode(["password"=>$pwd,"hash"=>$hash]);
