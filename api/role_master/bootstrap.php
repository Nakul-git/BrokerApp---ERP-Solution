<?php
require_once "../session.php";

function ensure_role_master_tables($con_company)
{
    $sqlRoles = "
        CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role_name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_by INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_role_name (role_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    if (!mysqli_query($con_company, $sqlRoles)) {
        throw new Exception("Failed to prepare roles table: " . mysqli_error($con_company));
    }

    $sqlPerms = "
        CREATE TABLE IF NOT EXISTS role_permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role_name VARCHAR(100) NOT NULL,
            module_name VARCHAR(191) NOT NULL,
            can_view TINYINT(1) NOT NULL DEFAULT 0,
            can_add TINYINT(1) NOT NULL DEFAULT 0,
            can_edit TINYINT(1) NOT NULL DEFAULT 0,
            can_delete TINYINT(1) NOT NULL DEFAULT 0,
            can_print TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_role_module (role_name, module_name),
            KEY idx_role_name (role_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    if (!mysqli_query($con_company, $sqlPerms)) {
        throw new Exception("Failed to prepare role permissions table: " . mysqli_error($con_company));
    }

}
