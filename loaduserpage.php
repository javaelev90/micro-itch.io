<?php
    
    require_once "class/ProjectRepository.php"; 
    require_once "class/FileUtil.php"; 
    
    require_once "authenticateSession.php";

    // Checks if user is logged in based on authenticateSession.php script
    if($isLoggedIn){
        $username = "";
        if($loggedInWithSession){
            $username = $_SESSION["username"];
        } else {
            $username = $_COOKIE["username"];
        }

        $projectRepository = new ProjectRepository();
        $projects = $projectRepository->getProjectByUsername($username)["data"];
        $projectImages = array();

        if(count($projects) > 0){
            // Creates projectid to cover-image dictionary for all the user's projects
            foreach($projects as $rowIndex => $row){
                $directoryPath = "./users/". $row["USERNAME"] . "/images/" . $row["ID"];
                $filePattern = "cover-image.*";
                $files = FileUtil::findFiles($directoryPath, $filePattern);
                if(count($files) > 0){
                    $projectImages[$row["ID"]] = $files[0];
                }
            }
        }
    }
    

?>