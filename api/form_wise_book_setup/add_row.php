<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$module_id = (int)($_POST['module_id'] ?? 0);
$description = trim($_POST['description'] ?? '');
$book = trim($_POST['book'] ?? '');
$numbering_type = trim($_POST['numbering_type'] ?? 'Auto');
$starting_no = trim($_POST['starting_no'] ?? '');
$end_no = trim($_POST['end_no'] ?? '');
$restart_numbering = trim($_POST['restart_numbering'] ?? 'Yearly');
$lock_date = trim($_POST['lock_date'] ?? '');
$active = trim($_POST['active'] ?? 'Y');
$division_name = trim($_POST['division_name'] ?? '');
$item_list = trim($_POST['item_list'] ?? '');
$cash_credit = trim($_POST['cash_credit'] ?? 'Both');

if ($module_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid module"]);
    exit;
}

if ($description === '' || $book === '') {
    echo json_encode(["status" => "error", "message" => "Description and Book are required"]);
    exit;
}

$numbering_type = (strcasecmp($numbering_type, 'Manual') === 0) ? 'Manual' : 'Auto';
$restart_opts = ['Yearly', 'Monthly', 'None'];
$restart_numbering = in_array($restart_numbering, $restart_opts, true) ? $restart_numbering : 'Yearly';
$active = (strcasecmp($active, 'N') === 0) ? 'N' : 'Y';
$cash_opts = ['Both', 'Cash', 'Credit'];
$cash_credit = in_array($cash_credit, $cash_opts, true) ? $cash_credit : 'Both';

$check_module = mysqli_prepare($con, "SELECT module_id FROM form_wise_entry_module WHERE module_id=? AND user_id=? LIMIT 1");
if (!$check_module) {
    echo json_encode(["status" => "error", "message" => "Module check failed"]);
    exit;
}
mysqli_stmt_bind_param($check_module, "ii", $module_id, $user_id);
mysqli_stmt_execute($check_module);
$mod_res = mysqli_stmt_get_result($check_module);
if (!$mod_res || !mysqli_fetch_assoc($mod_res)) {
    echo json_encode(["status" => "error", "message" => "Module not found"]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT setup_id FROM form_wise_book_setup
     WHERE user_id=? AND module_id=? AND LOWER(description)=LOWER(?) AND LOWER(book)=LOWER(?)
     LIMIT 1"
);
if ($dup) {
    mysqli_stmt_bind_param($dup, "iiss", $user_id, $module_id, $description, $book);
    mysqli_stmt_execute($dup);
    $dup_res = mysqli_stmt_get_result($dup);
    if ($dup_res && mysqli_fetch_assoc($dup_res)) {
        echo json_encode(["status" => "error", "message" => "Row already exists"]);
        exit;
    }
}

$stmt = mysqli_prepare(
    $con,
    "INSERT INTO form_wise_book_setup
        (module_id, description, book, numbering_type, starting_no, end_no, restart_numbering,
         lock_date, active, division_name, item_list, cash_credit, user_id)
     VALUES
        (?, ?, ?, ?, NULLIF(?, ''), NULLIF(?, ''), ?, NULLIF(?, ''), ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Insert prepare failed"]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "isssssssssssi",
    $module_id,
    $description,
    $book,
    $numbering_type,
    $starting_no,
    $end_no,
    $restart_numbering,
    $lock_date,
    $active,
    $division_name,
    $item_list,
    $cash_credit,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Row added",
        "setup_id" => mysqli_insert_id($con)
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Insert failed"]);
}
?>
