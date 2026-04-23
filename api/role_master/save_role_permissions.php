<?php

require_once "../session.php";

header("Content-Type: application/json");

/* =========================
   VALIDATE INPUT
========================= */

$role_name = isset($_POST["role_name"]) ? trim($_POST["role_name"]) : "";

if ($role_name === "") {

    echo json_encode([
        "status" => "error",
        "message" => "Role name missing"
    ]);

    exit;
}

/* =========================
   DELETE OLD PERMISSIONS
========================= */

$stmt = mysqli_prepare(
    $con_company,
    "DELETE FROM role_permissions WHERE role_name=?"
);

mysqli_stmt_bind_param($stmt,"s",$role_name);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


/* =========================
   SAVE SECTION RIGHTS
========================= */

if(isset($_POST["rights"])){

    foreach($_POST["rights"] as $module=>$perm){

        $view = isset($perm["v"]) ? 1 : 0;

        $add = 0;
        $edit = 0;
        $delete = 0;
        $print = 0;

        $stmt = mysqli_prepare(
            $con_company,
            "INSERT INTO role_permissions
            (role_name,module_name,can_view,can_add,can_edit,can_delete,can_print)
            VALUES (?,?,?,?,?,?,?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssiiiii",
            $role_name,
            $module,
            $view,
            $add,
            $edit,
            $delete,
            $print
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

}


/* =========================
   SAVE PAGE RIGHTS
========================= */

if(isset($_POST["page_rights"])){

    foreach($_POST["page_rights"] as $page=>$perm){

        $view   = isset($perm["v"]) ? 1 : 0;
        $add    = isset($perm["a"]) ? 1 : 0;
        $edit   = isset($perm["e"]) ? 1 : 0;
        $delete = isset($perm["d"]) ? 1 : 0;
        $print  = isset($perm["p"]) ? 1 : 0;

        $module = "PAGE:" . strtoupper($page);

        $stmt = mysqli_prepare(
            $con_company,
            "INSERT INTO role_permissions
            (role_name,module_name,can_view,can_add,can_edit,can_delete,can_print)
            VALUES (?,?,?,?,?,?,?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssiiiii",
            $role_name,
            $module,
            $view,
            $add,
            $edit,
            $delete,
            $print
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

}


/* =========================
   SUCCESS RESPONSE
========================= */

echo json_encode([
    "status" => "success"
]);

