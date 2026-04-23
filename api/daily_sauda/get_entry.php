<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

try {
    $entryNo = (int)($_GET['entry_no'] ?? 0);
    $entryDate = trim((string)($_GET['entry_date'] ?? date('Y-m-d')));
    $bookCode = trim((string)($_GET['book_code'] ?? 'SUD'));
    $entryChr = trim((string)($_GET['entry_chr'] ?? 'A'));

    if ($entryNo <= 0) {
        throw new Exception('Entry number is required');
    }
    if ($bookCode === '') $bookCode = 'SUD';
    if ($entryChr === '') $entryChr = 'A';

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();

    $headerStmt = mysqli_prepare(
        $con,
        "SELECT sno, DATE_FORMAT(d_a_t_e, '%Y-%m-%d') AS entry_date,
                c_j_s_p, vouc_chr, vouc_code, pcd AS buyer_id, party_name AS buyer_name,
                scode AS seller_id, bbcode AS sub_broker_id, rmks, load_req
         FROM etrans1
         WHERE co_code = ? AND div_code = ? AND br_code = ? AND yr = ?
           AND main_bk = 'SD' AND c_j_s_p = ? AND vouc_code = ? AND vouc_chr = ? AND del = 'N'
         LIMIT 1"
    );
    mysqli_stmt_bind_param($headerStmt, 'iiissis', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr'], $bookCode, $entryNo, $entryChr);
    mysqli_stmt_execute($headerStmt);
    $headerRes = mysqli_stmt_get_result($headerStmt);
    $header = $headerRes ? mysqli_fetch_assoc($headerRes) : null;
    mysqli_stmt_close($headerStmt);

    if (!$header) {
        throw new Exception('Entry not found');
    }

    $lookupStmt = mysqli_prepare($con_master, "SELECT party_name FROM party WHERE party_id = ? LIMIT 1");
    $lookupName = function ($id) use ($lookupStmt) {
        $partyId = (int)$id;
        if ($partyId <= 0) {
            return '';
        }
        mysqli_stmt_bind_param($lookupStmt, 'i', $partyId);
        mysqli_stmt_execute($lookupStmt);
        $res = mysqli_stmt_get_result($lookupStmt);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        return (string)($row['party_name'] ?? '');
    };

    $sellerName = $lookupName($header['seller_id'] ?? 0);
    $subBrokerName = $lookupName($header['sub_broker_id'] ?? 0);

    $itemStmt = mysqli_prepare(
        $con,
        "SELECT sno, sr_no, it_code AS product_id, item_name AS product_name,
                brnd_code AS brand_id, brand_name, qty, pck, wght, rate, amount, ratetyp, nar
         FROM etrans2
         WHERE etrans1_sno = ? AND main_bk = 'SDBYR' AND del = 'N'
         ORDER BY sr_no, sno"
    );
    $etrans1Sno = (int)$header['sno'];
    mysqli_stmt_bind_param($itemStmt, 'i', $etrans1Sno);
    mysqli_stmt_execute($itemStmt);
    $itemRes = mysqli_stmt_get_result($itemStmt);
    $items = [];
    while ($itemRes && ($row = mysqli_fetch_assoc($itemRes))) {
        $items[] = [
            'sno' => (int)($row['sno'] ?? 0),
            'sr_no' => (int)($row['sr_no'] ?? 0),
            'product_id' => (int)($row['product_id'] ?? 0),
            'product_name' => (string)($row['product_name'] ?? ''),
            'brand_id' => (int)($row['brand_id'] ?? 0),
            'brand_name' => (string)($row['brand_name'] ?? ''),
            'qty' => (float)($row['qty'] ?? 0),
            'pack' => (float)($row['pck'] ?? 0),
            'weight' => (float)($row['wght'] ?? 0),
            'rate' => (float)($row['rate'] ?? 0),
            'amount' => (float)($row['amount'] ?? 0),
            'rate_qw' => (string)($row['ratetyp'] ?? 'W'),
            'remark' => (string)($row['nar'] ?? '')
        ];
    }
    mysqli_stmt_close($itemStmt);
    mysqli_stmt_close($lookupStmt);
    mysqli_close($con);

    echo json_encode([
        'status' => 'success',
        'header' => [
            'etrans1_sno' => $etrans1Sno,
            'entry_date' => (string)($header['entry_date'] ?? ''),
            'book_code' => (string)($header['c_j_s_p'] ?? $bookCode),
            'entry_chr' => (string)($header['vouc_chr'] ?? $entryChr),
            'entry_no' => (int)($header['vouc_code'] ?? $entryNo),
            'buyer_id' => (int)($header['buyer_id'] ?? 0),
            'buyer_name' => (string)($header['buyer_name'] ?? ''),
            'seller_id' => (int)($header['seller_id'] ?? 0),
            'seller_name' => $sellerName,
            'sub_broker_id' => (int)($header['sub_broker_id'] ?? 0),
            'sub_broker_name' => $subBrokerName,
            'remarks' => (string)($header['rmks'] ?? ''),
            'loading_required' => (string)($header['load_req'] ?? 'N')
        ],
        'items' => $items
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
