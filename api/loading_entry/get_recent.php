<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

try {
    $entryDate = trim((string)($_GET['entry_date'] ?? date('Y-m-d')));
    $bookCode = trim((string)($_GET['book_code'] ?? 'LD001'));
    if ($bookCode === '') {
        $bookCode = 'LD001';
    }

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();

    $stmt = mysqli_prepare(
        $con,
        "SELECT DATE_FORMAT(d_a_t_e, '%Y-%m-%d') AS entry_date, c_j_s_p, vouc_chr, vouc_code, ref_book, ref_chr, ref_no, vehicle_no
         FROM etrans1
         WHERE co_code = ? AND div_code = ? AND br_code = ? AND yr = ?
           AND main_bk = 'DLV' AND c_j_s_p = ? AND del = 'N'
         ORDER BY vouc_code DESC
         LIMIT 30"
    );
    mysqli_stmt_bind_param($stmt, 'iiiss', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr'], $bookCode);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $rows = [];
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    echo json_encode([
        'status' => 'success',
        'rows' => $rows
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
