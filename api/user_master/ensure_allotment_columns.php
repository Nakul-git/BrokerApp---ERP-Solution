<?php

function ensure_user_allotment_columns(mysqli $con_company): void {
    $needed = [
        "allowed_divisions" => "ALTER TABLE users ADD COLUMN allowed_divisions TEXT NULL AFTER created_by",
        "allowed_companies" => "ALTER TABLE users ADD COLUMN allowed_companies TEXT NULL AFTER allowed_divisions"
    ];

    foreach ($needed as $column => $sql) {
        $safeColumn = mysqli_real_escape_string($con_company, $column);
        $check = mysqli_query($con_company, "SHOW COLUMNS FROM users LIKE '{$safeColumn}'");
        if ($check && mysqli_num_rows($check) > 0) {
            continue;
        }
        mysqli_query($con_company, $sql);
    }
}
