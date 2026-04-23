<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

try {
    $entryDate = trim((string)($_GET['entry_date'] ?? date('Y-m-d')));
    $bookCode = trim((string)($_GET['book_code'] ?? 'SUD'));
    if ($bookCode === '') $bookCode = 'SUD';

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();

    $stmt = mysqli_prepare(
        $con,
        "SELECT sno, vouc_code, vouc_chr, c_j_s_p,
                DATE_FORMAT(d_a_t_e, '%Y-%m-%d') AS entry_date,
                pcd AS buyer_id, party_name AS buyer_name,
                scode AS seller_id, rmks, load_req
         FROM etrans1
         WHERE co_code = ? AND div_code = ? AND br_code = ? AND yr = ?
           AND main_bk = 'SD' AND c_j_s_p = ? AND del = 'N'
         ORDER BY vouc_code DESC, sno DESC
         LIMIT 20"
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
