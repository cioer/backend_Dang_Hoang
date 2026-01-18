<?php
require_once __DIR__ . "/../bootstrap.php";

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors("GET");

$db = Bootstrap::db();

try {
    $query = "SELECT * FROM banners WHERE is_active = 1 ORDER BY priority DESC, created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $banners_arr = array();

    $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https" : "http";
    $host = $_SERVER["HTTP_HOST"];
    $base_url = $protocol . "://" . $host . "/";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        if (!empty($image_url) && strpos($image_url, "http") !== 0) {
            $image_url = $base_url . $image_url;
        }

        $banner_item = array(
            "id" => (int)$id,
            "image_url" => $image_url,
            "title" => $title,
            "cta_text" => $cta_text,
            "link_url" => $link_url,
            "is_active" => (int)$is_active,
            "priority" => (int)$priority
        );
        array_push($banners_arr, $banner_item);
    }

    Response::success($banners_arr);

} catch (Exception $e) {
    Response::success([]);
}
