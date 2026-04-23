<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

function sauda_num($value, $scale = 2)
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
    $bookCode = trim((string)($header['book_code'] ?? 'SUD'));
    $entryChr = trim((string)($header['entry_chr'] ?? 'A'));
    $entryNo = (int)($header['entry_no'] ?? 0);
    $buyerId = (int)($header['buyer_id'] ?? 0);
    $buyerName = trim((string)($header['buyer_name'] ?? ''));
    $sellerId = (int)($header['seller_id'] ?? 0);
    $sellerName = trim((string)($header['seller_name'] ?? ''));
    $subBrokerId = (int)($header['sub_broker_id'] ?? 0);
    $remarks = trim((string)($header['remarks'] ?? ''));
    $loadingRequired = strtoupper(trim((string)($header['loading_required'] ?? 'N'))) === 'Y' ? 'Y' : 'N';

    if ($entryDate === '') throw new Exception('Entry date is required');
    if ($entryNo <= 0) throw new Exception('Entry number is required');
    if ($bookCode === '') $bookCode = 'SUD';
    if ($entryChr === '') $entryChr = 'A';
    if ($buyerId <= 0) throw new Exception('Buyer is required');
    if ($sellerId <= 0) throw new Exception('Seller is required');
    if ($buyerId === $sellerId) throw new Exception('Buyer and seller must be different');

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();
    mysqli_begin_transaction($con);

    if (transaction_entry_exists($con, $ctx, 'etrans1', $entryNo, 'SD', $bookCode, $entryChr)) {
        throw new Exception('Entry already exists');
    }

    $preparedItems = [];
    $totalQty = 0.0;
    $totalWght = 0.0;
    $totalAmt = 0.0;
    $srNo = 1;

    foreach ($items as $item) {
        if (!is_array($item)) continue;
        $productId = (int)($item['product_id'] ?? 0);
        $productName = trim((string)($item['product_name'] ?? ''));
        $brandId = (int)($item['brand_id'] ?? 0);
        $brandName = trim((string)($item['brand_name'] ?? ''));
        $qty = sauda_num($item['qty'] ?? 0);
        $pack = sauda_num($item['pack'] ?? 0);
        $wght = sauda_num($item['weight'] ?? 0, 3);
        $rate = sauda_num($item['rate'] ?? 0);
        $amount = sauda_num($item['amount'] ?? ($qty * $rate));
        $rateQw = trim((string)($item['rate_qw'] ?? 'W'));
        $itemRemark = trim((string)($item['remark'] ?? ''));

        if ($productId <= 0 && $productName === '') continue;
        if ($qty <= 0 || $rate < 0) {
            throw new Exception('Each item must have valid product, qty, and rate');
        }
        if ($amount <= 0) $amount = sauda_num($qty * $rate);

        $preparedItems[] = [
            'sr_no' => $srNo,
            'product_id' => $productId,
            'product_name' => $productName,
            'brand_id' => $brandId,
            'brand_name' => $brandName,
            'qty' => $qty,
            'pack' => $pack,
            'wght' => $wght,
            'rate' => $rate,
            'amount' => $amount,
            'rate_qw' => $rateQw !== '' ? $rateQw : 'W',
            'remark' => $itemRemark
        ];
        $totalQty += $qty;
        $totalWght += $wght;
        $totalAmt += $amount;
        $srNo++;
    }

    if (!$preparedItems) {
        throw new Exception('At least one product row is required');
    }

    $headerStmt = mysqli_prepare(
        $con,
        "INSERT INTO etrans1
        (br_code, div_code, co_code, yr, main_bk, c_j_s_p, vouc_code, vouc_chr, d_a_t_e, pcd, party_name, rmks, gr_amt, amt, scode, bbcode, load_req, sd_byracsno, sd_slracsno, created_by)
         VALUES (?, ?, ?, ?, 'SD', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param(
        $headerStmt,
        'iiissississddiisiii',
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
        $subBrokerId,
        $loadingRequired,
        $buyerId,
        $sellerId,
        $userId
    );
    if (!mysqli_stmt_execute($headerStmt)) {
        throw new Exception('Failed saving sauda header: ' . mysqli_stmt_error($headerStmt));
    }
    $etrans1Id = (int)mysqli_insert_id($con);
    mysqli_stmt_close($headerStmt);

    $detailSql = "INSERT INTO etrans2
        (etrans1_sno, br_code, div_code, co_code, yr, main_bk, c_j_s_p, vouc_code, vouc_chr, sr_no, sddate, p_code, it_code, item_name, brnd_code, brand_name, pck, wght, qty, d_c, rate, ratetyp, type, amount, nar, stk_flag, bcode, sbcode, bbcode, oppcode, pend_qty, brk_yn, pay_yn, loading_req, os_book, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $detailStmt = mysqli_prepare($con, $detailSql);
    $detailTypes = str_repeat('s', 36);

    $buyerDetailIds = [];
    $sellerDetailIds = [];

    foreach ($preparedItems as $item) {
        $mainBk = 'SDBYR';
        $dc = 'C';
        $type = 0;
        $stkFlag = 'SD';
        $osBook = 'SD';
        $brkYn = 'N';
        $payYn = 'N';
        $oppcode = 0;
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
            $item['wght'],
            $item['qty'],
            $dc,
            $item['rate'],
            $item['rate_qw'],
            $type,
            $item['amount'],
            $item['remark'],
            $stkFlag,
            $sellerId,
            $subBrokerId,
            $subBrokerId,
            $oppcode,
            $item['qty'],
            $brkYn,
            $payYn,
            $loadingRequired,
            $osBook,
            $userId
        );
        if (!mysqli_stmt_execute($detailStmt)) {
            throw new Exception('Failed saving buyer detail: ' . mysqli_stmt_error($detailStmt));
        }
        $buyerDetailIds[] = (int)mysqli_insert_id($con);
    }

    foreach ($preparedItems as $index => $item) {
        $mainBk = 'SDSLR';
        $dc = 'C';
        $type = 0;
        $stkFlag = 'SD';
        $osBook = 'SD';
        $brkYn = 'N';
        $payYn = 'N';
        $oppcode = (int)($buyerDetailIds[$index] ?? 0);
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
            $sellerId,
            $item['product_id'],
            $item['product_name'],
            $item['brand_id'],
            $item['brand_name'],
            $item['pack'],
            $item['wght'],
            $item['qty'],
            $dc,
            $item['rate'],
            $item['rate_qw'],
            $type,
            $item['amount'],
            $item['remark'],
            $stkFlag,
            $buyerId,
            $subBrokerId,
            $subBrokerId,
            $oppcode,
            $item['qty'],
            $brkYn,
            $payYn,
            $loadingRequired,
            $osBook,
            $userId
        );
        if (!mysqli_stmt_execute($detailStmt)) {
            throw new Exception('Failed saving seller detail: ' . mysqli_stmt_error($detailStmt));
        }
        $sellerDetailIds[] = (int)mysqli_insert_id($con);
    }
    mysqli_stmt_close($detailStmt);

    if ($buyerDetailIds && $sellerDetailIds) {
        $updOpp = mysqli_prepare($con, "UPDATE etrans2 SET oppcode = ? WHERE sno = ?");
        foreach ($buyerDetailIds as $idx => $buyerDetailId) {
            $sellerDetailId = (int)($sellerDetailIds[$idx] ?? 0);
            if ($sellerDetailId <= 0) continue;
            mysqli_stmt_bind_param($updOpp, 'ii', $sellerDetailId, $buyerDetailId);
            mysqli_stmt_execute($updOpp);
        }
        mysqli_stmt_close($updOpp);
    }

    add_transaction_log($con, $userId, 'Daily Sauda', 'A', $etrans1Id, 'etrans1', 'Sauda Voucher ' . $entryNo);
    mysqli_commit($con);
    mysqli_close($con);

    echo json_encode([
        'status' => 'success',
        'message' => 'Daily Sauda saved',
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
