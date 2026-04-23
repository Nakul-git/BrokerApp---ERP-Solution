<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_id = (int)($_POST['party_id'] ?? 0);

function has_table($con, $table) {
    $table_safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW TABLES LIKE '{$table_safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

if ($party_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid party'
    ]);
    exit;
}

$stmt = mysqli_prepare($con, 'DELETE FROM party_brokerage_rate WHERE user_id=? AND party_id=?');
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $party_id);

if (mysqli_stmt_execute($stmt)) {
    if (has_table($con, 'party_brokerage_packing_rate')) {
        $stmt2 = mysqli_prepare($con, 'DELETE FROM party_brokerage_packing_rate WHERE user_id=? AND party_id=?');
        if ($stmt2) {
            mysqli_stmt_bind_param($stmt2, 'ii', $user_id, $party_id);
            mysqli_stmt_execute($stmt2);
        }
    }
    echo json_encode([
        'status' => 'success',
        'message' => 'Deleted'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Delete failed'
    ]);
}
?>
