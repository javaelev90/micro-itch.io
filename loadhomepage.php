<?php
    
    require_once "class/ProjectRepository.php"; 
    require_once "class/FileUtil.php"; 
    
    $projectRepository = new ProjectRepository();
    $numberOfProjects = 15;
    $projects = $projectRepository->getNumberOfProjects($numberOfProjects)["data"];
    $projectImages = array();

    // Retrieves up to 15 projects
    if(count($projects) > 0){
        // Maps the images paths to the projectids
        foreach($projects as $rowIndex => $row){
            $directoryPath = "./users/". $row["USERNAME"] . "/images/" . $row["ID"];
            $filePattern = "cover-image.*";
            $files = FileUtil::findFiles($directoryPath, $filePattern);
            if(count($files) > 0){
                $projectImages[$row["ID"]] = $files[0];
            }
        }
    }

?>