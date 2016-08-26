<?php

//START SESSION IF SESSION IS NOT ALREADY ACTIVE
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//IF A SESSIONID IS SET (USER LOGGED IN) RETURN ALL SESSION INFO + loggedin = true
if (!empty($_SESSION["userid"]) || !empty($_SESSION["teacherid"])){
    $_SESSION["loggedin"] = 'true';
    $arr = $_SESSION;
} else {//IF A SESSIONID IS NOT SET RETURN loggedin = false
    $_SESSION["loggedin"] = 'false';
    $arr = $_SESSION;
}
//RETURN ALL VALUES AS JSON OBJECTS
echo json_encode($arr);

?>