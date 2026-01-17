<?php
include_once '../../config/database.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

try {
    // Read from banners table (New Schema)
    $query = "SELECT * FROM banners WHERE is_active = 1 ORDER BY priority DESC, created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $banners_arr = array();
    
    // Determine base URL
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    // Adjust path to point to Backend root
    // Assuming this script is in Backend/api/banners/
    $host = $_SERVER['HTTP_HOST'];
    $script_dir = dirname($_SERVER['PHP_SELF']); // e.g., /Backend/api/banners
    // We need to go up 2 levels to get /Backend/
    // Easier: Just hardcode /Backend/ if we know the structure, or use relative calculation.
    // Let's rely on the image_url being stored as "uploads/banners/..."
    
    $base_url = $protocol . "://" . $host . "/Backend/";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        
        // Fix image URL if it's relative (stored as 'uploads/banners/xyz.jpg')
        if (!empty($image_url) && strpos($image_url, 'http') !== 0) {
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
    
    echo json_encode($banners_arr);

} catch (Exception $e) {
    // If banners table doesn't exist, return empty
    echo json_encode([]);
}
?>
