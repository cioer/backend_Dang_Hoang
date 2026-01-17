<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Core\{Middleware, Response, Bootstrap};

Middleware::cors('POST');
Middleware::requireAdmin();

$db = Bootstrap::db();

$updated = 0;
$stmtSel = $db->query("SELECT id, username FROM users WHERE is_locked=0");
$users = $stmtSel->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $u) {
    $req = null;
    $pref = substr($u['username'], 0, 3);
    if ($pref === 'GV-') $req = 'teacher';
    elseif ($pref === 'HS-') $req = 'student';
    elseif ($pref === 'PH-') $req = 'parent';
    if ($req) {
        $stmtUpd = $db->prepare("UPDATE users SET role=:r WHERE id=:id");
        if ($stmtUpd->execute([":r" => $req, ":id" => $u['id']])) $updated++;
    }
}

Response::success(["message" => "Roles normalized", "updated" => $updated]);
