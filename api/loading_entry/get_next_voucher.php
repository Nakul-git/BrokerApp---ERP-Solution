<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

try {
    $entryDate = trim((string)($_GET['entry_date'] ?? date('Y-m-d')));
    $bookCode = trim((string)($_GET['book_code'] ?? 'LD001'));
    $entryChr = trim((string)($_GET['entry_chr'] ?? 'A'));

    if ($bookCode === '') {
        $bookCode = 'LD001';
    }
    if ($entryChr === '') {
        $entryChr = 'A';
    }

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();
    $nextNo = next_etrans_voucher_number($con, $ctx, 'DLV', $bookCode, $entryChr);
    mysqli_close($con);

    echo json_encode([
        'status' => 'success',
        'next_no' => $nextNo
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
