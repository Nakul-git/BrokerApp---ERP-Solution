<?php

function get_master_scope_user_id() {
    $envVal = getenv('MASTER_SHARED_USER_ID');
    if ($envVal !== false && is_numeric($envVal)) {
        $id = (int)$envVal;
        if ($id > 0) {
            return $id;
        }
    }
    return 1;
}

