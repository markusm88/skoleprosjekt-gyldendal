<?php

//START SESSION IF SESSION IS NOT ALREADY ACTIVE
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//DESTROY SESSION AND SAVE SUCCESS
$success = session_destroy();

//RETURN REPORT OF SUCCESS- true / false
return $success;

?>