<?php
session_start();

// Clear division selection from session
unset($_SESSION["selected_division_id"]);
unset($_SESSION["selected_division_code"]);
unset($_SESSION["selected_division_name"]);

// Return success
http_response_code(200);
echo json_encode(["status" => "success", "message" => "Division selection cleared"]);
exit();
?>