<?php
require_once __DIR__ . '/session.php';

function brokerage_txn_db_name()
{
    $env = getenv('BROKERAGE_TXN_DB');
    if (is_string($env) && trim($env) !== '') {
        return trim($env);
    }
    return 'brokerage_txn';
}

function brokerage_fin_year_code($date = null)
{
    $ts = $date ? strtotime((string)$date) : time();
    if (!$ts) {
        $ts = time();
    }

    $year = (int)date('Y', $ts);
    $month = (int)date('n', $ts);
    if ($month >= 4) {
        $start = $year;
        $end = $year + 1;
    } else {
        $start = $year - 1;
        $end = $year;
    }

    return sprintf('%02d-%02d', $start % 100, $end % 100);
}

function brokerage_connect_txn_root()
{
    global $server, $username, $password, $port;
    $link = mysqli_connect($server, $username, $password, null, $port);
    if (!$link) {
        throw new Exception('Transaction DB root connection failed: ' . mysqli_connect_error());
    }
    mysqli_set_charset($link, 'utf8mb4');
    return $link;
}

function brokerage_connect_txn_db()
{
    global $server, $username, $password, $port;
    $db = brokerage_txn_db_name();
    $link = mysqli_connect($server, $username, $password, $db, $port);
    if (!$link) {
        throw new Exception('Transaction DB connection failed: ' . mysqli_connect_error());
    }
    mysqli_set_charset($link, 'utf8mb4');
    return $link;
}

function brokerage_exec_or_throw($con, $sql, $message)
{
    if (!mysqli_query($con, $sql)) {
        throw new Exception($message . ': ' . mysqli_error($con));
    }
}

function brokerage_column_exists($con, $tableName, $columnName)
{
    $table = mysqli_real_escape_string($con, (string)$tableName);
    $column = mysqli_real_escape_string($con, (string)$columnName);
    $res = mysqli_query($con, "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $res && mysqli_num_rows($res) > 0;
}

function ensure_transaction_tables()
{
    static $ready = false;
    if ($ready) {
        return;
    }

    $root = brokerage_connect_txn_root();
    $db = brokerage_txn_db_name();
    brokerage_exec_or_throw($root, "CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci", 'Failed creating transaction database');
    mysqli_close($root);

    $con = brokerage_connect_txn_db();

    $queries = [];

    $queries[] = "
        CREATE TABLE IF NOT EXISTS voucher_series (
            id INT AUTO_INCREMENT PRIMARY KEY,
            co_code INT NOT NULL DEFAULT 0,
            div_code INT NOT NULL DEFAULT 0,
            br_code INT NOT NULL DEFAULT 1,
            yr VARCHAR(10) NOT NULL,
            main_bk VARCHAR(10) NOT NULL,
            c_j_s_p VARCHAR(10) NOT NULL,
            vouc_chr VARCHAR(5) NOT NULL DEFAULT 'A',
            last_no INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_voucher_series (co_code, div_code, br_code, yr, main_bk, c_j_s_p, vouc_chr)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    $queries[] = "
        CREATE TABLE IF NOT EXISTS etrans1 (
            sno INT AUTO_INCREMENT PRIMARY KEY,
            br_code INT NOT NULL DEFAULT 1,
            div_code INT NOT NULL DEFAULT 0,
            co_code INT NOT NULL DEFAULT 0,
            yr VARCHAR(10) NOT NULL,
            main_bk VARCHAR(10) NOT NULL,
            c_j_s_p VARCHAR(10) NOT NULL,
            vouc_code INT NOT NULL,
            vouc_chr VARCHAR(5) NOT NULL DEFAULT 'A',
            d_a_t_e DATETIME DEFAULT NULL,
            pcd INT NOT NULL DEFAULT 0,
            party_name VARCHAR(150) DEFAULT NULL,
            rmks TEXT DEFAULT NULL,
            gr_amt DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            amt DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            scode INT NOT NULL DEFAULT 0,
            bbcode INT NOT NULL DEFAULT 0,
            load_req CHAR(1) NOT NULL DEFAULT 'N',
            sd_byracsno INT NOT NULL DEFAULT 0,
            sd_slracsno INT NOT NULL DEFAULT 0,
            del CHAR(1) NOT NULL DEFAULT 'N',
            cncl CHAR(1) NOT NULL DEFAULT 'N',
            created_by INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_etrans1_voucher (co_code, div_code, br_code, yr, main_bk, c_j_s_p, vouc_code, vouc_chr),
            KEY idx_etrans1_header (co_code, div_code, main_bk, d_a_t_e)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    $queries[] = "
        CREATE TABLE IF NOT EXISTS etrans2 (
            sno INT AUTO_INCREMENT PRIMARY KEY,
            etrans1_sno INT NOT NULL,
            br_code INT NOT NULL DEFAULT 1,
            div_code INT NOT NULL DEFAULT 0,
            co_code INT NOT NULL DEFAULT 0,
            yr VARCHAR(10) NOT NULL,
            main_bk VARCHAR(10) NOT NULL,
            c_j_s_p VARCHAR(10) NOT NULL,
            vouc_code INT NOT NULL,
            vouc_chr VARCHAR(5) NOT NULL DEFAULT 'A',
            sr_no INT NOT NULL DEFAULT 0,
            sddate DATETIME DEFAULT NULL,
            p_code INT NOT NULL DEFAULT 0,
            it_code INT NOT NULL DEFAULT 0,
            item_name VARCHAR(150) DEFAULT NULL,
            brnd_code INT NOT NULL DEFAULT 0,
            brand_name VARCHAR(150) DEFAULT NULL,
            pck DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            wght DECIMAL(14,3) NOT NULL DEFAULT 0.000,
            qty DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            d_c CHAR(2) NOT NULL DEFAULT 'D',
            rate DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            ratetyp VARCHAR(10) DEFAULT NULL,
            type INT NOT NULL DEFAULT 0,
            amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            nar VARCHAR(255) DEFAULT NULL,
            stk_flag VARCHAR(10) DEFAULT NULL,
            bcode INT NOT NULL DEFAULT 0,
            sbcode INT NOT NULL DEFAULT 0,
            bbcode INT NOT NULL DEFAULT 0,
            oppcode INT NOT NULL DEFAULT 0,
            pend_qty DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            brk_yn CHAR(1) NOT NULL DEFAULT 'N',
            pay_yn CHAR(1) NOT NULL DEFAULT 'N',
            loading_req CHAR(1) NOT NULL DEFAULT 'N',
            os_book VARCHAR(10) DEFAULT NULL,
            del CHAR(1) NOT NULL DEFAULT 'N',
            cncl CHAR(1) NOT NULL DEFAULT 'N',
            created_by INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_etrans2_header (etrans1_sno),
            KEY idx_etrans2_voucher (co_code, div_code, br_code, yr, main_bk, c_j_s_p, vouc_code, vouc_chr)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    $queries[] = "
        CREATE TABLE IF NOT EXISTS trans (
            sno INT AUTO_INCREMENT PRIMARY KEY,
            co_code INT NOT NULL DEFAULT 0,
            yr VARCHAR(10) NOT NULL,
            br_code INT NOT NULL DEFAULT 1,
            div_code INT NOT NULL DEFAULT 0,
            main_bk VARCHAR(10) NOT NULL,
            c_j_s_p VARCHAR(10) NOT NULL,
            vouc_code INT NOT NULL DEFAULT 0,
            vouc_chr VARCHAR(5) NOT NULL DEFAULT 'A',
            d_a_t_e DATETIME DEFAULT NULL,
            pcd INT NOT NULL DEFAULT 0,
            tcd INT NOT NULL DEFAULT 0,
            amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            gramt DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            d_c CHAR(1) NOT NULL DEFAULT 'D',
            nar VARCHAR(500) DEFAULT NULL,
            del CHAR(1) NOT NULL DEFAULT 'N',
            cncl CHAR(1) NOT NULL DEFAULT 'N',
            created_by INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_trans_voucher (co_code, div_code, br_code, yr, main_bk, c_j_s_p, vouc_code, vouc_chr)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    $queries[] = "
        CREATE TABLE IF NOT EXISTS outstanding (
            sno INT AUTO_INCREMENT PRIMARY KEY,
            co_code INT NOT NULL DEFAULT 0,
            yr VARCHAR(10) NOT NULL,
            br_code INT NOT NULL DEFAULT 1,
            div_code INT NOT NULL DEFAULT 0,
            main_bk VARCHAR(10) NOT NULL,
            c_j_s_p VARCHAR(10) NOT NULL,
            vouc_code INT NOT NULL DEFAULT 0,
            vouc_chr VARCHAR(5) NOT NULL DEFAULT 'A',
            d_a_t_e DATETIME DEFAULT NULL,
            pcd INT NOT NULL DEFAULT 0,
            amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            amt_paid DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            amt_rec DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            bill_bal DECIMAL(14,2) NOT NULL DEFAULT 0.00,
            del CHAR(1) NOT NULL DEFAULT 'N',
            cncl CHAR(1) NOT NULL DEFAULT 'N',
            created_by INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_outstanding_voucher (co_code, div_code, br_code, yr, main_bk, c_j_s_p, vouc_code, vouc_chr)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    $queries[] = "
        CREATE TABLE IF NOT EXISTS user_log (
            sno INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL DEFAULT 0,
            module_name VARCHAR(100) NOT NULL,
            user_operation CHAR(1) NOT NULL,
            ent_sno INT NOT NULL DEFAULT 0,
            table_name VARCHAR(100) NOT NULL,
            descr VARCHAR(255) DEFAULT NULL,
            user_ipadd VARCHAR(100) DEFAULT NULL,
            user_machinenm VARCHAR(100) DEFAULT NULL,
            ent_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    foreach ($queries as $query) {
        brokerage_exec_or_throw($con, $query, 'Failed preparing transaction table');
    }

    $etran1Adds = [
        "ref_main_bk VARCHAR(10) DEFAULT NULL",
        "ref_book VARCHAR(10) DEFAULT NULL",
        "ref_chr VARCHAR(5) DEFAULT NULL",
        "ref_no INT NOT NULL DEFAULT 0",
        "vehicle_no VARCHAR(50) DEFAULT NULL",
        "transport_id INT NOT NULL DEFAULT 0",
        "transport_name VARCHAR(150) DEFAULT NULL"
    ];
    foreach ($etran1Adds as $definition) {
        $parts = preg_split('/\s+/', trim($definition));
        $columnName = $parts[0] ?? '';
        if ($columnName !== '' && !brokerage_column_exists($con, 'etrans1', $columnName)) {
            brokerage_exec_or_throw($con, "ALTER TABLE etrans1 ADD COLUMN {$definition}", "Failed extending etrans1 with {$columnName}");
        }
    }

    mysqli_close($con);
    $ready = true;
}

function get_transaction_connection()
{
    ensure_transaction_tables();
    return brokerage_connect_txn_db();
}

function get_transaction_context($entryDate = null)
{
    $coCode = (int)($_SESSION['selected_company_id'] ?? 0);
    $divCode = (int)($_SESSION['selected_division_id'] ?? 0);
    if ($coCode <= 0) {
        throw new Exception('Please select a company first.');
    }

    return [
        'co_code' => $coCode,
        'div_code' => $divCode,
        'br_code' => 1,
        'yr' => brokerage_fin_year_code($entryDate),
        'company_name' => (string)($_SESSION['selected_company_name'] ?? ''),
        'division_name' => (string)($_SESSION['selected_division_name'] ?? '')
    ];
}

function add_transaction_log($con, $userId, $moduleName, $operation, $entitySno, $tableName, $descr = '')
{
    $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
    $machine = (string)($_SERVER['REMOTE_HOST'] ?? gethostname() ?? '');
    $stmt = mysqli_prepare(
        $con,
        "INSERT INTO user_log (user_id, module_name, user_operation, ent_sno, table_name, descr, user_ipadd, user_machinenm)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'ississss', $userId, $moduleName, $operation, $entitySno, $tableName, $descr, $ip, $machine);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed writing user log: ' . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

function transaction_entry_exists($con, array $ctx, $tableName, $entryNo, $mainBk = 'SD', $cjsp = 'SUD', $entryChr = 'A')
{
    $table = strtolower(trim((string)$tableName));
    if ($table !== 'etrans1') {
        throw new Exception('Unsupported transaction table lookup');
    }

    $stmt = mysqli_prepare(
        $con,
        "SELECT 1
         FROM etrans1
         WHERE co_code = ? AND div_code = ? AND br_code = ? AND yr = ?
           AND main_bk = ? AND c_j_s_p = ? AND vouc_code = ? AND vouc_chr = ? AND del = 'N'
         LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, 'iiisssis', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr'], $mainBk, $cjsp, $entryNo, $entryChr);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $exists = $res && mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    return (bool)$exists;
}

function next_etrans_voucher_number($con, array $ctx, $mainBk, $cjsp, $voucChr = 'A')
{
    $stmt = mysqli_prepare(
        $con,
        "SELECT COALESCE(MAX(vouc_code), 0) + 1 AS next_no
         FROM etrans1
         WHERE co_code = ? AND div_code = ? AND br_code = ? AND yr = ?
           AND main_bk = ? AND c_j_s_p = ? AND vouc_chr = ? AND del = 'N'"
    );
    mysqli_stmt_bind_param($stmt, 'iiissss', $ctx['co_code'], $ctx['div_code'], $ctx['br_code'], $ctx['yr'], $mainBk, $cjsp, $voucChr);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);
    return (int)($row['next_no'] ?? 1);
}
?>
