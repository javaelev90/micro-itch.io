<?php
    
    require_once "class/ProjectRepository.php"; 
    require_once "class/Util.php"; 
    require_once "class/FileUtil.php"; 
    require_once "authenticateSession.php";
    
    $projectRepository = new ProjectRepository();
    $projectname = "";
    $projectUrl = "";
    $project = "";
    $image = "";

    // Checks that the needed projectid is supplied
    if(isset($_GET["projectid"])) {
        $projectid = $_GET["projectid"];
        $project = $projectRepository->getProjectById($projectid)["data"];

        if(count($project) > 0) {
            $username = $project[0]["USERNAME"];
            $projectname = $project[0]["PROJECTNAME"];

            // Creates the project file Url and cover image url
            $projectUrl = "users/" . $username . "/projects/" . $projectid . "/index.html";
            $imageDirectory = "./users/". $username . "/images/" . $projectid;
            
            $filePattern = "cover-image.*";
            $image = FileUtil::findFiles($imageDirectory, $filePattern);
        } else {
            Util::redirect("index.php");
        }
    } else {
        Util::redirect("index.php");
    }

?>