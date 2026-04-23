<?php
header("Content-Type: application/json");
require "../session.php";
require_once "../master_scope.php";

$user_id = get_master_scope_user_id();

$raw = file_get_contents("php://input");
$payload = json_decode($raw, true);
if (!is_array($payload)) {
    echo json_encode([ "status" => "error", "message" => "Invalid payload" ]);
    exit;
}

$group_fix_id = isset($payload["group_fix_id"]) ? (int)$payload["group_fix_id"] : 0;
$allowed = $payload["allowed_group_ids"] ?? [];
if ($group_fix_id <= 0 || !is_array($allowed)) {
    echo json_encode([ "status" => "error", "message" => "Invalid data" ]);
    exit;
}

$allowed = array_values(array_unique(array_filter(array_map("intval", $allowed), function ($v) { return $v > 0; })));
$allowed_json = json_encode($allowed);

$check = mysqli_prepare($con, "SELECT setup_id FROM group_setup WHERE user_id=? AND group_fix_id=? LIMIT 1");
if (!$check) {
    echo json_encode([ "status" => "error", "message" => "Prepare failed", "error" => mysqli_error($con) ]);
    exit;
}
mysqli_stmt_bind_param($check, "ii", $user_id, $group_fix_id);
mysqli_stmt_execute($check);
$res = mysqli_stmt_get_result($check);
$row = $res ? mysqli_fetch_assoc($res) : null;

if ($row && isset($row["setup_id"])) {
    $setup_id = (int)$row["setup_id"];
    $stmt = mysqli_prepare($con, "UPDATE group_setup SET allowed_group_ids_json=?, updated_at=NOW() WHERE setup_id=? AND user_id=?");
    if (!$stmt) {
        echo json_encode([ "status" => "error", "message" => "Prepare failed", "error" => mysqli_error($con) ]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "sii", $allowed_json, $setup_id, $user_id);
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode([ "status" => "error", "message" => "Failed", "error" => mysqli_error($con) ]);
        exit;
    }
} else {
    $stmt = mysqli_prepare($con, "INSERT INTO group_setup (group_fix_id, allowed_group_ids_json, user_id) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode([ "status" => "error", "message" => "Prepare failed", "error" => mysqli_error($con) ]);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "isi", $group_fix_id, $allowed_json, $user_id);
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode([ "status" => "error", "message" => "Failed", "error" => mysqli_error($con) ]);
        exit;
    }
}

echo json_encode([ "status" => "success", "message" => "Saved" ]);
?>
