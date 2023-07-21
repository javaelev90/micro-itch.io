<?php
    require_once "startsession.php";
    require_once "authenticateSession.php";

    require_once "class/CommentRepository.php";
    require_once "class/FormUtil.php";

    // Gets the input value else returns an empty string
    function getRequiredFormInput($inputName){
        if(!isset($_POST[$inputName]) || empty(trim($_POST[$inputName]))){
            return "";
        } else {
            return trim($_POST[$inputName]);
        }
    }
    
    // Checks if user is logged in based on authenticateSession.php script
    if(!$isLoggedIn) {
        Util::redirect("login.php");
    }

    $username = "";
    // Get username
    if(!empty($_SESSION["username"])) {
        $username = $_SESSION["username"];
    } else if(!empty($_COOKIE["username"])) {
        $username = $_COOKIE["username"];
    } 

    $projectid = htmlspecialchars(getRequiredFormInput("projectid"), ENT_QUOTES);
    $parentid = htmlspecialchars(getRequiredFormInput("parentid"), ENT_QUOTES);
    $comment = htmlspecialchars(getRequiredFormInput("comment"), ENT_QUOTES);
    
    if($projectid != "" && $comment != "" && $username != ""){

        $commentRepository = new CommentRepository();

        // Doing some format checks
        if(is_numeric($projectid)){
            // If this is set it is a reply to a commment
            if(is_numeric($parentid)){
                // Getting parent comment for ROOTID
                $commentRows = $commentRepository->getComment($parentid, $projectid)["data"];
                if(count($commentRows) > 0){
                    $rootid = isset($commentRows[0]["ROOTID"]) ? $commentRows[0]["ROOTID"] : $commentRows[0]["ID"];
                    $result = $commentRepository->insertReplyComment($username, $projectid, $rootid, $parentid, $comment);
                }
            }
            // Else it is a regular comment
            else {
                $result = $commentRepository->insertComment($username, $projectid, $comment);
            }
        }
    }
    Util::redirect("projectpage.php?projectid=$projectid");

?>