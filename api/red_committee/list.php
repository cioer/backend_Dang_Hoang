<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('GET');

$user = Middleware::auth();
$db = Bootstrap::db();

$role = $user->role;
$classId = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;
$area = $_GET['area'] ?? null;

if ($role === 'teacher' && $classId) {
    $stmt = $db->prepare("SELECT 1 FROM schedule WHERE teacher_id=:tid AND class_id=:cid LIMIT 1");
    $stmt->execute([':tid' => $user->id, ':cid' => $classId]);
    if (!$stmt->fetch()) {
        Response::forbidden('Forbidden');
    }
}

$params = [];
$sql = "SELECT m.id, m.user_id, m.class_id, m.area, m.active, m.expired_at, m.duration_weeks, m.start_date, DATEDIFF(m.expired_at, NOW()) as days_left, u.full_name, u.username, c.name as class_name FROM red_committee_members m JOIN users u ON m.user_id=u.id JOIN classes c ON m.class_id = c.id WHERE m.active=1";

if ($classId) {
    $sql .= " AND m.class_id=:cid";
    $params[':cid'] = $classId;
}
if ($area) {
    $sql .= " AND m.area=:area";
    $params[':area'] = $area;
}
$sql .= " ORDER BY u.full_name";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check expiration status
foreach ($rows as &$row) {
    if ($row['days_left'] < 0) {
        $row['status_text'] = 'Da het han';
        $row['status_color'] = '#D32F2F'; // Red
    } else if ($row['days_left'] <= 3) {
        $row['status_text'] = 'Sap het han (' . $row['days_left'] . ' ngay)';
        $row['status_color'] = '#F57C00'; // Orange
    } else {
        $row['status_text'] = 'Dang hoat dong (' . $row['days_left'] . ' ngay)';
        $row['status_color'] = '#388E3C'; // Green
    }
}

Response::success($rows);
