<?php
require "../session.php";

header("Content-Type: application/json");

$response = [
    "status" => "success"
];

try {

    if (!isset($_POST['user_id'])) {
        throw new Exception("User ID missing");
    }

    $user_id = intval($_POST['user_id']);

    if ($user_id <= 0) {
        throw new Exception("Invalid user ID");
    }

    // section-level permissions (view only)
    $rights = $_POST['rights'] ?? [];
    // page-level permissions (v/a/e/d/p)
    $page_rights = $_POST['page_rights'] ?? [];

    // 🔥 delete old permissions first
    $deleteSql = "DELETE FROM user_permissions WHERE user_id = ?";
    $stmt = mysqli_prepare($con_company, $deleteSql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!empty($rights) || !empty($page_rights)) {

        $insertSql = "INSERT INTO user_permissions 
            (user_id, module_name, can_view, can_add, can_edit, can_delete, can_print)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con_company, $insertSql);

        // Save section rows (V only)
        foreach ($rights as $module => $perm) {

            $can_view   = isset($perm['v']) ? 1 : 0;
            $can_add    = 0;
            $can_edit   = 0;
            $can_delete = 0;
            $can_print  = 0;

            if ($can_view !== 1) {
                continue;
            }

            mysqli_stmt_bind_param(
                $stmt,
                "isiiiii",
                $user_id,
                $module,
                $can_view,
                $can_add,
                $can_edit,
                $can_delete,
                $can_print
            );

            mysqli_stmt_execute($stmt);
        }

        // Save page rows (full V/A/E/D/P)
        foreach ($page_rights as $page => $perm) {

            $can_view   = isset($perm['v']) ? 1 : 0;
            $can_add    = isset($perm['a']) ? 1 : 0;
            $can_edit   = isset($perm['e']) ? 1 : 0;
            $can_delete = isset($perm['d']) ? 1 : 0;
            $can_print  = isset($perm['p']) ? 1 : 0;

            if (($can_view + $can_add + $can_edit + $can_delete + $can_print) === 0) {
                continue;
            }

            $normalized_page = strtoupper(trim($page));
            $normalized_page = preg_replace("/[^A-Z0-9]+/", "_", $normalized_page);
            $module_name = "PAGE:" . $normalized_page;

            mysqli_stmt_bind_param(
                $stmt,
                "isiiiii",
                $user_id,
                $module_name,
                $can_view,
                $can_add,
                $can_edit,
                $can_delete,
                $can_print
            );

            mysqli_stmt_execute($stmt);
        }

        mysqli_stmt_close($stmt);
    }

} catch (Exception $e) {

    $response["status"] = "error";
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
