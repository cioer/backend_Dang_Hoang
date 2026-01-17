<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$data = Request::all();

if (!empty($data['filename']) && !empty($data['file_data'])) {
    $db = Bootstrap::db();

    try {
        $db->beginTransaction();

        $query = "INSERT INTO image_archives (filename, file_size, mime_type, file_data) VALUES (:filename, :file_size, :mime_type, :file_data)";
        $stmt = $db->prepare($query);

        $stmt->bindParam(":filename", $data['filename']);
        $stmt->bindParam(":file_size", $data['file_size']);
        $stmt->bindParam(":mime_type", $data['mime_type']);
        $stmt->bindParam(":file_data", $data['file_data']);

        if ($stmt->execute()) {
            $db->commit();
            Response::message("Image imported successfully.", 201);
        } else {
            $db->rollBack();
            Response::error("Unable to import image.", 503);
        }
    } catch (Exception $e) {
        $db->rollBack();
        Response::serverError("Error: " . $e->getMessage());
    }
} else {
    Response::error("Incomplete data.", 400);
}
