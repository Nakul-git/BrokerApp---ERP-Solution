<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../transaction_bootstrap.php';

try {
    $entryNo = (int)($_GET['entry_no'] ?? 0);
    $entryDate = trim((string)($_GET['entry_date'] ?? date('Y-m-d')));
    $bookCode = trim((string)($_GET['book_code'] ?? 'SUD'));
    $entryChr = trim((string)($_GET['entry_chr'] ?? 'A'));
    if ($bookCode === '') $bookCode = 'SUD';
    if ($entryChr === '') $entryChr = 'A';

    $ctx = get_transaction_context($entryDate);
    $con = get_transaction_connection();
    $exists = $entryNo > 0 ? transaction_entry_exists($con, $ctx, 'etrans1', $entryNo, 'SD', $bookCode, $entryChr) : false;
    mysqli_close($con);

    echo json_encode([
        'status' => 'success',
        'exists' => $exists
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'exists' => false,
        'message' => $e->getMessage()
    ]);
}
?>
