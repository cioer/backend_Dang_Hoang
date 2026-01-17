<?php
include_once '../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Change link_url to TEXT to support long URLs (up to 65,535 chars)
    $sql = "ALTER TABLE banners MODIFY link_url TEXT";
    
    $db->exec($sql);
    echo "Successfully updated 'link_url' column to TEXT type.";
} catch(PDOException $e) {
    echo "Error updating table: " . $e->getMessage();
}
?>
