<?php
require_once __DIR__ . '/../config/database.php';

function readData($path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if ($ext === 'json') {
        $json = file_get_contents($path);
        $data = json_decode($json, true);
        if (!is_array($data)) return [];
        return $data;
    } elseif ($ext === 'csv') {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle);
            while (($d = fgetcsv($handle)) !== false) {
                $row = array_combine($header, $d);
                $rows[] = $row;
            }
            fclose($handle);
        }
        return $rows;
    }
    return [];
}

function validateFormat($code) {
    return preg_match('/^HS-\d{4}$/', $code) === 1;
}

function validateDob($dob) {
    $ts = strtotime($dob);
    if ($ts === false) return false;
    $y = (int)date('Y', $ts);
    return $y >= 2005 && $y <= 2015;
}

function validateName($name) {
    return preg_match('/^[A-Za-zÀ-ỹ\\s]+$/u', $name) === 1 && mb_strlen($name) >= 3;
}

function checkAgainstDb($conn, $codes) {
    if (empty($codes)) return [];
    $placeholders = implode(',', array_fill(0, count($codes), '?'));
    $stmt = $conn->prepare("SELECT username FROM users WHERE role='student' AND username IN ($placeholders)");
    foreach ($codes as $i => $c) $stmt->bindValue($i + 1, $c);
    $stmt->execute();
    $exists = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    return $exists;
}

function checkNameDobAgainstDb($conn, $pairs) {
    if (empty($pairs)) return [];
    $results = [];
    $stmt = $conn->prepare("SELECT full_name, username FROM users WHERE role='student' AND DATE_FORMAT(created_at,'%Y') IS NOT NULL");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $set = [];
    foreach ($rows as $r) {
        $set[$r['full_name']] = true;
    }
    foreach ($pairs as $p) {
        if (isset($set[$p['full_name']])) $results[] = $p['full_name'];
    }
    return array_values(array_unique($results));
}

$path = $argv[1] ?? __DIR__ . '/../data/new_students_100.json';
$data = readData($path);
if (empty($data)) {
    echo "ERROR: No data loaded\n";
    exit(1);
}

$codes = [];
$internalDupCodes = [];
$formatErrors = [];
$dobErrors = [];
$nameErrors = [];

foreach ($data as $row) {
    $code = $row['code'];
    $name = $row['full_name'];
    $dob = $row['dob'];
    if (!validateFormat($code)) $formatErrors[] = $code;
    if (!validateDob($dob)) $dobErrors[] = $code;
    if (!validateName($name)) $nameErrors[] = $code;
    if (isset($codes[$code])) $internalDupCodes[] = $code;
    $codes[$code] = true;
}

$db = new Database();
$conn = $db->getConnection();
$existingCodes = checkAgainstDb($conn, array_keys($codes));
$nameDupDb = checkNameDobAgainstDb($conn, array_map(function($r){ return ['full_name'=>$r['full_name']]; }, $data));

$report = [
    'total_new' => count($data),
    'format_errors' => array_values(array_unique($formatErrors)),
    'dob_errors' => array_values(array_unique($dobErrors)),
    'name_errors' => array_values(array_unique($nameErrors)),
    'internal_duplicate_codes' => array_values(array_unique($internalDupCodes)),
    'existing_codes_in_db' => $existingCodes,
    'existing_names_in_db' => $nameDupDb
];

echo json_encode($report, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
