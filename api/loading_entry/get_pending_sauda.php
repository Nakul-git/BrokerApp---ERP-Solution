<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

try {
    $entryDate = trim((string)($_GET['entry_date'] ?? date('Y-m-d')));
    $search = trim((string)($_GET['q'] ?? ''));

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();

    $sql = "SELECT h.vouc_code, h.vouc_chr, h.c_j_s_p, DATE_FORMAT(h.d_a_t_e, '%Y-%m-%d') AS entry_date,
                   h.party_name AS buyer_name, h.scode AS seller_id, h.bbcode AS sub_broker_id, h.load_req,
                   SUM(CASE WHEN d.main_bk = 'SDBYR' THEN d.pend_qty ELSE 0 END) AS pending_qty
            FROM etrans1 h
            INNER JOIN etrans2 d ON d.etrans1_sno = h.sno AND d.main_bk = 'SDBYR' AND d.del = 'N'
            WHERE h.co_code = ? AND h.div_code = ? AND h.br_code = ? AND h.yr = ?
              AND h.main_bk = 'SD' AND h.del = 'N'
              AND d.pend_qty > 0";
    if ($search !== '') {
        $sql .= " AND (h.party_name LIKE CONCAT('%', ?, '%') OR CAST(h.vouc_code AS CHAR) LIKE CONCAT('%', ?, '%'))";
    }
    $sql .= " GROUP BY h.sno, h.vouc_code, h.vouc_chr, h.c_j_s_p, h.d_a_t_e, h.party_name, h.scode, h.bbcode, h.load_req
              ORDER BY h.vouc_code DESC
              LIMIT 25";

    $stmt = mysqli_prepare($con, $sql);
    if ($search !== '') {
        mysqli_stmt_bind_param($stmt, 'iiisss', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr'], $search, $search);
    } else {
        mysqli_stmt_bind_param($stmt, 'iiis', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr']);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $rows = [];
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $sellerId = (int)($row['seller_id'] ?? 0);
        $sellerName = '';
        if ($sellerId > 0) {
            $lookupStmt = mysqli_prepare($con_master, "SELECT party_name FROM party WHERE party_id = ? LIMIT 1");
            mysqli_stmt_bind_param($lookupStmt, 'i', $sellerId);
            mysqli_stmt_execute($lookupStmt);
            $lookupRes = mysqli_stmt_get_result($lookupStmt);
            $lookupRow = $lookupRes ? mysqli_fetch_assoc($lookupRes) : null;
            $sellerName = (string)($lookupRow['party_name'] ?? '');
            mysqli_stmt_close($lookupStmt);
        }
        $row['seller_name'] = $sellerName;
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
