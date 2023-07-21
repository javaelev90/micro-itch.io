<?php

    require_once "startsession.php";
    require_once "authenticateSession.php";
    require_once "loadproject.php";

    require_once "class/Util.php"; 
    require_once "class/FileUtil.php"; 
    require_once "class/ProjectRepository.php"; 
    require_once "class/CommentRepository.php"; 

    // Checks if user is logged in based on authenticateSession.php script
    if(!$isLoggedIn) {
        Util::redirect("login.php");
    }

    $isProjectOwner = false;
    // Authenticate project ownership
    if($project != "" && $isLoggedIn) {
        if($loggedInWithSession && $_SESSION["username"] != $project[0]["USERNAME"]) {
            $isProjectOwner = false;
        } else if($loggedInWithCookie && $_COOKIE["username"] != $project[0]["USERNAME"]) {
            $isProjectOwner = false;
        } else {
            // User is verified, continue to edit project
            $isProjectOwner = true;
        }
    }

    if($isProjectOwner && $projectid != ""){

        $username = $project[0]["USERNAME"];
        $projectDirectory = "users/" . $username . "/projects/" . $projectid;
        $imageDirectory = "users/". $username . "/images/" . $projectid;

        // Deletes the project database data
        if($projectRepository->deleteProject($projectid)["resultcode"] == -1){
        }
        // Deletes the project files
        FileUtil::deleteDirectoryAndContent($projectDirectory);
        FileUtil::deleteDirectoryAndContent($imageDirectory);

        $commentRepository = new CommentRepository();

        // Deletes the comments for the project
        if($commentRepository->deleteCommentsForProject($projectid)["resultcode"] == -1){
        }
    }
    Util::redirect("index.php");

?>