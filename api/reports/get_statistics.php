<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Request, Bootstrap};

Middleware::cors('POST');

$user = Middleware::auth();
$db = Bootstrap::db();
$data = Request::all();

$user_id = $user->id;
$role = $user->role;

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
        Response::error('Error loading student data', 500);
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

    Response::success([
        "scores" => $scores,
        "attendance" => $attendance
    ]);
} catch (PDOException $e) {
    error_log("Statistics query error: " . $e->getMessage());
    Response::error('Error fetching statistics', 500);
}
