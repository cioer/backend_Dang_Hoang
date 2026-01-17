<?php
require_once '../red_committee/util.php';
$database = new Database();
$db = $database->getConnection();

// Deactivate expired red committee members
$query = "UPDATE red_committee_members 
          SET active = 0 
          WHERE active = 1 AND expired_at < CURDATE()";
$stmt = $db->prepare($query);
$stmt->execute();
$count = $stmt->rowCount();

if ($count > 0) {
    // Log the event
    $log = $db->prepare("INSERT INTO audit_logs(action, details) VALUES('auto_expire_red_star', :details)");
    $log->execute([':details' => "Deactivated $count expired red star members"]);
}

// Return stats for admin dashboard if requested
if (isset($_GET['stats'])) {
    header('Content-Type: application/json');
    echo json_encode(['deactivated_count' => $count]);
}
?>