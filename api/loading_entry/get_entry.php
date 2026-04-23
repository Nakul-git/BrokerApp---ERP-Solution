<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

try {
    $entryNo = (int)($_GET['entry_no'] ?? 0);
    $entryDate = trim((string)($_GET['entry_date'] ?? date('Y-m-d')));
    $bookCode = trim((string)($_GET['book_code'] ?? 'LD001'));
    $entryChr = trim((string)($_GET['entry_chr'] ?? 'A'));

    if ($entryNo <= 0) {
        throw new Exception('Entry number is required');
    }

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();

    $headerStmt = mysqli_prepare(
        $con,
        "SELECT sno, DATE_FORMAT(d_a_t_e, '%Y-%m-%d') AS entry_date, c_j_s_p, vouc_chr, vouc_code,
                pcd AS buyer_id, party_name AS buyer_name, scode AS seller_id, rmks,
                ref_book, ref_chr, ref_no, vehicle_no, transport_id, transport_name
         FROM etrans1
         WHERE co_code = ? AND div_code = ? AND br_code = ? AND yr = ?
           AND main_bk = 'DLV' AND c_j_s_p = ? AND vouc_code = ? AND vouc_chr = ? AND del = 'N'
         LIMIT 1"
    );
    mysqli_stmt_bind_param($headerStmt, 'iiissis', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr'], $bookCode, $entryNo, $entryChr);
    mysqli_stmt_execute($headerStmt);
    $headerRes = mysqli_stmt_get_result($headerStmt);
    $header = $headerRes ? mysqli_fetch_assoc($headerRes) : null;
    mysqli_stmt_close($headerStmt);
    if (!$header) {
        throw new Exception('Loading entry not found');
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

    $sourceHeader = null;
    if ((int)($header['ref_no'] ?? 0) > 0) {
        $sourceStmt = mysqli_prepare(
            $con,
            "SELECT pcd AS buyer_id, party_name AS buyer_name, scode AS seller_id
             FROM etrans1
             WHERE co_code = ? AND div_code = ? AND br_code = ? AND yr = ?
               AND main_bk = 'SD' AND c_j_s_p = ? AND vouc_code = ? AND vouc_chr = ? AND del = 'N'
             LIMIT 1"
        );
        mysqli_stmt_bind_param($sourceStmt, 'iiissis', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr'], $header['ref_book'], $header['ref_no'], $header['ref_chr']);
        mysqli_stmt_execute($sourceStmt);
        $sourceRes = mysqli_stmt_get_result($sourceStmt);
        $sourceHeader = $sourceRes ? mysqli_fetch_assoc($sourceRes) : null;
        mysqli_stmt_close($sourceStmt);
    }

    $itemStmt = mysqli_prepare(
        $con,
        "SELECT sr_no, it_code AS product_id, item_name AS product_name, brnd_code AS brand_id, brand_name,
                qty, pck, wght, rate, amount, nar, oppcode
         FROM etrans2
         WHERE etrans1_sno = ? AND main_bk = 'DLV' AND del = 'N'
         ORDER BY sr_no, sno"
    );
    $etrans1Sno = (int)$header['sno'];
    mysqli_stmt_bind_param($itemStmt, 'i', $etrans1Sno);
    mysqli_stmt_execute($itemStmt);
    $itemRes = mysqli_stmt_get_result($itemStmt);
    $items = [];
    while ($itemRes && ($row = mysqli_fetch_assoc($itemRes))) {
        $sourceDetailSno = (int)($row['oppcode'] ?? 0);
        $pendingQty = 0.0;
        if ($sourceDetailSno > 0) {
            $pendingStmt = mysqli_prepare($con, "SELECT pend_qty FROM etrans2 WHERE sno = ? LIMIT 1");
            mysqli_stmt_bind_param($pendingStmt, 'i', $sourceDetailSno);
            mysqli_stmt_execute($pendingStmt);
            $pendingRes = mysqli_stmt_get_result($pendingStmt);
            $pendingRow = $pendingRes ? mysqli_fetch_assoc($pendingRes) : null;
            $pendingQty = (float)($pendingRow['pend_qty'] ?? 0);
            mysqli_stmt_close($pendingStmt);
        }
        $items[] = [
            'source_detail_sno' => $sourceDetailSno,
            'sr_no' => (int)($row['sr_no'] ?? 0),
            'product_id' => (int)($row['product_id'] ?? 0),
            'product_name' => (string)($row['product_name'] ?? ''),
            'brand_id' => (int)($row['brand_id'] ?? 0),
            'brand_name' => (string)($row['brand_name'] ?? ''),
            'pending_qty' => $pendingQty,
            'qty' => (float)($row['qty'] ?? 0),
            'pack' => (float)($row['pck'] ?? 0),
            'weight' => (float)($row['wght'] ?? 0),
            'rate' => (float)($row['rate'] ?? 0),
            'amount' => (float)($row['amount'] ?? 0),
            'remark' => (string)($row['nar'] ?? '')
        ];
    }

    mysqli_stmt_close($itemStmt);
    mysqli_stmt_close($lookupStmt);
    mysqli_close($con);

    echo json_encode([
        'status' => 'success',
        'header' => [
            'entry_date' => (string)($header['entry_date'] ?? ''),
            'book_code' => (string)($header['c_j_s_p'] ?? 'LD001'),
            'entry_chr' => (string)($header['vouc_chr'] ?? 'A'),
            'entry_no' => (int)($header['vouc_code'] ?? 0),
            'buyer_id' => (int)($sourceHeader['buyer_id'] ?? $header['buyer_id'] ?? 0),
            'buyer_name' => (string)($sourceHeader['buyer_name'] ?? $header['buyer_name'] ?? ''),
            'seller_id' => (int)($sourceHeader['seller_id'] ?? $header['seller_id'] ?? 0),
            'seller_name' => $sellerName,
            'sauda_book' => (string)($header['ref_book'] ?? 'SUD'),
            'sauda_chr' => (string)($header['ref_chr'] ?? 'A'),
            'sauda_no' => (int)($header['ref_no'] ?? 0),
            'vehicle_no' => (string)($header['vehicle_no'] ?? ''),
            'transport_id' => (int)($header['transport_id'] ?? 0),
            'transport_name' => (string)($header['transport_name'] ?? ''),
            'remarks' => (string)($header['rmks'] ?? '')
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
