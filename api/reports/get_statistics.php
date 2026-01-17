<?php
include_once '../../config/database.php';
include_once '../../config/jwt.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$token = isset($data->token) ? $data->token : "";

$decoded = validateJwt($token);
if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Truy cập bị từ chối."]);
    exit;
}

$user_id = $decoded['data']->id;
$role = $decoded['data']->role;

$student_id = $user_id;
if ($role == 'parent') {
    // Get student_id linked to parent
    try {
        $query = "SELECT student_id FROM parent_student_links WHERE parent_id = :parent_id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":parent_id", $user_id);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $student_id = $row['student_id'];
        }
    } catch (PDOException $e) {
        error_log("Error getting parent's student: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(["message" => "Error loading student data"]);
        exit;
    }
}

try {
    // 1. Get Scores for BarChart
    $queryScores = "SELECT s.name as subject_name, COALESCE(sc.score_final, sc.score_45m, sc.score_15m, 0) as score 
                    FROM scores sc
                    JOIN subjects s ON sc.subject_id = s.id
                    WHERE sc.student_id = :student_id
                    ORDER BY s.name";
    $stmtScores = $db->prepare($queryScores);
    $stmtScores->bindParam(":student_id", $student_id);
    $stmtScores->execute();
    $scores = $stmtScores->fetchAll(PDO::FETCH_ASSOC);

    // 2. Get Attendance for PieChart
    $queryAttendance = "SELECT status, COUNT(*) as count 
                        FROM attendance 
                        WHERE student_id = :student_id 
                        GROUP BY status";
    $stmtAttendance = $db->prepare($queryAttendance);
    $stmtAttendance->bindParam(":student_id", $student_id);
    $stmtAttendance->execute();
    $attendance = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "scores" => $scores,
        "attendance" => $attendance
    ]);
} catch (PDOException $e) {
    error_log("Statistics query error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Error fetching statistics"]);
    exit;
}
