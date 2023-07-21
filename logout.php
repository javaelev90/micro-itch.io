<?php

    require_once "startsession.php";
    require_once "class/Util.php"; 
    $util = new Util();

    //Clear session
    unset($_SESSION["user_id"]);
    unset($_SESSION["username"]);
    unset($_SESSION["expirytime"]);
    session_destroy();

    //Remove cookies
    $util->clearCookieAuthentication();

    $util->redirect("index.php");

?>