<?php
include_once '../../config/database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, title, summary, image_url, created_at FROM news ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$news_list = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $news_item = array(
        "id" => $id,
        "title" => $title,
        "summary" => $summary,
        "image_url" => $image_url,
        "created_at" => $created_at
    );
    array_push($news_list, $news_item);
}

http_response_code(200);
echo json_encode($news_list);
?>
