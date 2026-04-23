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

$source_group_name = trim($payload["source_group_name"] ?? "");
$target_group_name = trim($payload["target_group_name"] ?? "");
$source_items = $payload["source_items"] ?? [];
$target_items = $payload["target_items"] ?? [];

if ($source_group_name === "" || $target_group_name === "") {
    echo json_encode([ "status" => "error", "message" => "Missing source/target group" ]);
    exit;
}
if (!is_array($source_items) || !is_array($target_items)) {
    echo json_encode([ "status" => "error", "message" => "Invalid items" ]);
    exit;
}

mysqli_begin_transaction($con);

try {
    $accStmt = mysqli_prepare($con, "UPDATE account_master SET group_name=? WHERE account_id=? AND user_id=?");
    $partyStmt = mysqli_prepare($con, "UPDATE party SET group_name=? WHERE party_id=? AND user_id=?");

    if (!$accStmt || !$partyStmt) {
        throw new Exception("Prepare failed: " . mysqli_error($con));
    }

    $accCount = 0;
    $partyCount = 0;

    $applyList = function ($items, $groupName) use ($accStmt, $partyStmt, $user_id, $con, &$accCount, &$partyCount) {
        foreach ($items as $item) {
            $type = isset($item["type"]) ? strtolower(trim($item["type"])) : "";
            $id = isset($item["id"]) ? (int)$item["id"] : 0;
            if ($id <= 0) continue;

            if ($type === "account") {
                mysqli_stmt_bind_param($accStmt, "sii", $groupName, $id, $user_id);
                if (!mysqli_stmt_execute($accStmt)) {
                    throw new Exception("Account update failed: " . mysqli_error($con));
                }
                $accCount += mysqli_stmt_affected_rows($accStmt);
            } elseif ($type === "party") {
                mysqli_stmt_bind_param($partyStmt, "sii", $groupName, $id, $user_id);
                if (!mysqli_stmt_execute($partyStmt)) {
                    throw new Exception("Party update failed: " . mysqli_error($con));
                }
                $partyCount += mysqli_stmt_affected_rows($partyStmt);
            }
        }
    };

    $applyList($source_items, $source_group_name);
    $applyList($target_items, $target_group_name);

    mysqli_commit($con);
    echo json_encode([
        "status" => "success",
        "message" => "Transfer saved",
        "accounts_updated" => $accCount,
        "parties_updated" => $partyCount
    ]);
} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode([ "status" => "error", "message" => $e->getMessage() ]);
}
?>
