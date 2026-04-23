<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

function load_num($value, $scale = 2)
{
    return round((float)($value ?? 0), $scale);
}

try {
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        throw new Exception('Invalid request payload');
    }

    $userId = (int)($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        throw new Exception('Unauthorized');
    }

    $header = is_array($payload['header'] ?? null) ? $payload['header'] : [];
    $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];

    $entryDate = trim((string)($header['entry_date'] ?? ''));
    $bookCode = trim((string)($header['book_code'] ?? 'LD001'));
    $entryChr = trim((string)($header['entry_chr'] ?? 'A'));
    $entryNo = (int)($header['entry_no'] ?? 0);
    $saudaBook = trim((string)($header['sauda_book'] ?? 'SUD'));
    $saudaChr = trim((string)($header['sauda_chr'] ?? 'A'));
    $saudaNo = (int)($header['sauda_no'] ?? 0);
    $buyerId = (int)($header['buyer_id'] ?? 0);
    $buyerName = trim((string)($header['buyer_name'] ?? ''));
    $sellerId = (int)($header['seller_id'] ?? 0);
    $transportId = (int)($header['transport_id'] ?? 0);
    $transportName = trim((string)($header['transport_name'] ?? ''));
    $vehicleNo = trim((string)($header['vehicle_no'] ?? ''));
    $remarks = trim((string)($header['remarks'] ?? ''));

    if ($entryDate === '') throw new Exception('Loading date is required');
    if ($entryNo <= 0) throw new Exception('Loading number is required');
    if ($saudaNo <= 0) throw new Exception('Linked sauda is required');
    if ($buyerId <= 0) throw new Exception('Buyer is required');
    if ($sellerId <= 0) throw new Exception('Seller is required');
    if ($bookCode === '') $bookCode = 'LD001';
    if ($entryChr === '') $entryChr = 'A';
    if ($saudaBook === '') $saudaBook = 'SUD';
    if ($saudaChr === '') $saudaChr = 'A';

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();
    mysqli_begin_transaction($con);

    if (transaction_entry_exists($con, $ctx, 'etrans1', $entryNo, 'DLV', $bookCode, $entryChr)) {
        throw new Exception('Loading entry already exists');
    }

    $sourceHeaderStmt = mysqli_prepare(
        $con,
        "SELECT sno
         FROM etrans1
         WHERE co_code = ? AND div_code = ? AND br_code = ? AND yr = ?
           AND main_bk = 'SD' AND c_j_s_p = ? AND vouc_code = ? AND vouc_chr = ? AND del = 'N'
         LIMIT 1"
    );
    mysqli_stmt_bind_param($sourceHeaderStmt, 'iiissis', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr'], $saudaBook, $saudaNo, $saudaChr);
    mysqli_stmt_execute($sourceHeaderStmt);
    $sourceHeaderRes = mysqli_stmt_get_result($sourceHeaderStmt);
    $sourceHeader = $sourceHeaderRes ? mysqli_fetch_assoc($sourceHeaderRes) : null;
    mysqli_stmt_close($sourceHeaderStmt);
    if (!$sourceHeader) {
        throw new Exception('Linked sauda was not found');
    }

    $preparedItems = [];
    $totalQty = 0.0;
    $totalWght = 0.0;
    $totalAmt = 0.0;
    $srNo = 1;

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $sourceDetailSno = (int)($item['source_detail_sno'] ?? 0);
        $sellerDetailSno = (int)($item['seller_detail_sno'] ?? 0);
        $productId = (int)($item['product_id'] ?? 0);
        $productName = trim((string)($item['product_name'] ?? ''));
        $brandId = (int)($item['brand_id'] ?? 0);
        $brandName = trim((string)($item['brand_name'] ?? ''));
        $pendingQty = load_num($item['pending_qty'] ?? 0);
        $loadQty = load_num($item['qty'] ?? 0);
        $pack = load_num($item['pack'] ?? 0);
        $weight = load_num($item['weight'] ?? 0, 3);
        $rate = load_num($item['rate'] ?? 0);
        $brokerage = load_num($item['brokerage'] ?? 0);
        $freight = load_num($item['freight'] ?? 0);
        $amount = load_num($item['amount'] ?? ($loadQty * $rate));
        $remark = trim((string)($item['remark'] ?? ''));

        if ($sourceDetailSno <= 0 || $loadQty <= 0) {
            continue;
        }
        if ($loadQty > $pendingQty) {
            throw new Exception('Load quantity cannot exceed pending quantity');
        }

        $preparedItems[] = [
            'source_detail_sno' => $sourceDetailSno,
            'seller_detail_sno' => $sellerDetailSno,
            'product_id' => $productId,
            'product_name' => $productName,
            'brand_id' => $brandId,
            'brand_name' => $brandName,
            'pending_qty' => $pendingQty,
            'qty' => $loadQty,
            'pack' => $pack,
            'weight' => $weight,
            'rate' => $rate,
            'brokerage' => $brokerage,
            'freight' => $freight,
            'amount' => $amount,
            'remark' => $remark,
            'sr_no' => $srNo++
        ];
        $totalQty += $loadQty;
        $totalWght += $weight;
        $totalAmt += $amount;
    }

    if (!$preparedItems) {
        throw new Exception('Enter at least one loading row');
    }

    $headerStmt = mysqli_prepare(
        $con,
        "INSERT INTO etrans1
        (br_code, div_code, co_code, yr, main_bk, c_j_s_p, vouc_code, vouc_chr, d_a_t_e, pcd, party_name, rmks, gr_amt, amt, scode, bbcode, ref_main_bk, ref_book, ref_chr, ref_no, vehicle_no, transport_id, transport_name, created_by)
         VALUES (?, ?, ?, ?, 'DLV', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'SD', ?, ?, ?, ?, ?, ?, ?)"
    );
    $headerTypes = str_repeat('s', 22);
    mysqli_stmt_bind_param(
        $headerStmt,
        $headerTypes,
        $ctx['br_code'],
        $ctx['div_code'],
        $ctx['co_code'],
        $ctx['yr'],
        $bookCode,
        $entryNo,
        $entryChr,
        $entryDate,
        $buyerId,
        $buyerName,
        $remarks,
        $totalAmt,
        $totalAmt,
        $sellerId,
        $transportId,
        $saudaBook,
        $saudaChr,
        $saudaNo,
        $vehicleNo,
        $transportId,
        $transportName,
        $userId
    );
    if (!mysqli_stmt_execute($headerStmt)) {
        throw new Exception('Failed saving loading header: ' . mysqli_stmt_error($headerStmt));
    }
    $etrans1Id = (int)mysqli_insert_id($con);
    mysqli_stmt_close($headerStmt);

    $detailSql = "INSERT INTO etrans2
        (etrans1_sno, br_code, div_code, co_code, yr, main_bk, c_j_s_p, vouc_code, vouc_chr, sr_no, sddate, p_code, it_code, item_name, brnd_code, brand_name, pck, wght, qty, d_c, rate, ratetyp, type, amount, nar, stk_flag, bcode, sbcode, bbcode, oppcode, pend_qty, brk_yn, pay_yn, loading_req, os_book, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $detailStmt = mysqli_prepare($con, $detailSql);
    $detailTypes = str_repeat('s', 36);

    foreach ($preparedItems as $item) {
        $mainBk = 'DLV';
        $dc = 'D';
        $type = 0;
        $stkFlag = 'DLV';
        $osBook = 'SD';
        $loadFlag = 'Y';
        $brkYn = $item['brokerage'] > 0 ? 'Y' : 'N';
        $payYn = $item['freight'] > 0 ? 'Y' : 'N';
        $remaining = max(0, load_num($item['pending_qty'] - $item['qty']));
        $detailNarr = trim($item['remark'] . ($vehicleNo !== '' ? ' | Vehicle: ' . $vehicleNo : ''));
        mysqli_stmt_bind_param(
            $detailStmt,
            $detailTypes,
            $etrans1Id,
            $ctx['br_code'],
            $ctx['div_code'],
            $ctx['co_code'],
            $ctx['yr'],
            $mainBk,
            $bookCode,
            $entryNo,
            $entryChr,
            $item['sr_no'],
            $entryDate,
            $buyerId,
            $item['product_id'],
            $item['product_name'],
            $item['brand_id'],
            $item['brand_name'],
            $item['pack'],
            $item['weight'],
            $item['qty'],
            $dc,
            $item['rate'],
            'W',
            $type,
            $item['amount'],
            $detailNarr,
            $stkFlag,
            $buyerId,
            $sellerId,
            $transportId,
            $item['source_detail_sno'],
            $remaining,
            $brkYn,
            $payYn,
            $loadFlag,
            $osBook,
            $userId
        );
        if (!mysqli_stmt_execute($detailStmt)) {
            throw new Exception('Failed saving loading detail: ' . mysqli_stmt_error($detailStmt));
        }

        $newPending = $remaining;
        $updatePending = mysqli_prepare($con, "UPDATE etrans2 SET pend_qty = ? WHERE sno = ?");
        mysqli_stmt_bind_param($updatePending, 'di', $newPending, $item['source_detail_sno']);
        mysqli_stmt_execute($updatePending);
        mysqli_stmt_close($updatePending);

        if ((int)$item['seller_detail_sno'] > 0) {
            $updateSellerPending = mysqli_prepare($con, "UPDATE etrans2 SET pend_qty = ? WHERE sno = ?");
            mysqli_stmt_bind_param($updateSellerPending, 'di', $newPending, $item['seller_detail_sno']);
            mysqli_stmt_execute($updateSellerPending);
            mysqli_stmt_close($updateSellerPending);
        }
    }

    mysqli_stmt_close($detailStmt);

    add_transaction_log($con, $userId, 'Loading Entry', 'A', $etrans1Id, 'etrans1', 'Loading Voucher ' . $entryNo);
    mysqli_commit($con);
    mysqli_close($con);

    echo json_encode([
        'status' => 'success',
        'message' => 'Loading entry saved',
        'etrans1_sno' => $etrans1Id,
        'voucher_no' => $entryNo
    ]);
} catch (Throwable $e) {
    if (isset($con) && $con instanceof mysqli) {
        mysqli_rollback($con);
        mysqli_close($con);
    }
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
