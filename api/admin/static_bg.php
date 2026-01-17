<?php
// This file generates placeholder background images - no bootstrap needed
// It outputs an image directly, not JSON

header("Access-Control-Allow-Origin: *");
header("Content-Type: image/png");

$size = isset($_GET['size']) ? $_GET['size'] : '800x400';
if (!preg_match('/^(\d+)x(\d+)$/', $size, $m)) {
    $m = [0, 800, 400];
}

$w = max(64, intval($m[1]));
$h = max(64, intval($m[2]));
$text = isset($_GET['text']) ? preg_replace('/[^a-zA-Z0-9_\- ]/', '', $_GET['text']) : 'bg';

$bg = imagecreatetruecolor($w, $h);
$c1 = imagecolorallocate($bg, 230, 235, 240);
$c2 = imagecolorallocate($bg, 60, 120, 200);
imagefilledrectangle($bg, 0, 0, $w, $h, $c1);

for ($y = 0; $y < $h; $y += 8) {
    imageline($bg, 0, $y, $w, $y, $c2);
}

$black = imagecolorallocate($bg, 0, 0, 0);
$font = 5;
$tw = imagefontwidth($font) * strlen($text);
$th = imagefontheight($font);
$tx = intval(($w - $tw) / 2);
$ty = intval(($h - $th) / 2);

imagestring($bg, $font, max(0, $tx), max(0, $ty), $text, $black);
imagepng($bg);
imagedestroy($bg);
