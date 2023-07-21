<?php
    require_once "startsession.php";
    require_once "authenticateSession.php";
    
    require_once "class/Util.php"; 
    require_once "class/FileUtil.php";
    require_once "class/FormUtil.php";
    require_once "class/ProjectRepository.php";  

    function sendErrorMessageAndExit($errorMsg, $sessionErrorMessageVariable){
        $_SESSION[$sessionErrorMessageVariable] = $errorMsg;
        Util::redirect("createproject.php");
    }

    $projectRepository = new ProjectRepository();

    // Checks if user is logged in based on authenticateSession.php script
    if(!$isLoggedIn) {
        Util::redirect("login.php");
    }

    if(!empty($_POST["create"])) {

        //STEPS IN THE SAVE PROJECT PROCESS
        //check in data
        //  If erroneous redirect to createproject with error message
        //create folder for user if not exists
        //create project folder for user if not exists
        //save zip file in project folder
        //unzip file 
        //  check if index file exists
        //      if not return error to user
        //          remove project folder
        //      else
        //          store project data in database

        $sessionErrorMessageVariable = "create-project-error-msg";
        $redirectUrl = "createproject.php";
        // Verify and retrieve the form inputs
        $projectName = htmlspecialchars(FormUtil::verifyInputLength(FormUtil::getRequiredFormInput("projectname", $sessionErrorMessageVariable, $redirectUrl), 254, "project name", $sessionErrorMessageVariable, $redirectUrl), ENT_QUOTES);
        $catchphrase = htmlspecialchars(FormUtil::verifyInputLength(FormUtil::getFormInput("catchphrase"), 80, "catch phrase",$sessionErrorMessageVariable, $redirectUrl), ENT_QUOTES);
        $description = htmlspecialchars(FormUtil::verifyInputLength(FormUtil::getRequiredFormInput("description", $sessionErrorMessageVariable, $redirectUrl), 4000, "description", $sessionErrorMessageVariable, $redirectUrl), ENT_QUOTES);
        $fileInputName = htmlspecialchars(FormUtil::getRequiredFileInput("projectfile", $sessionErrorMessageVariable, $redirectUrl), ENT_QUOTES);
        $imageInputName = htmlspecialchars(FormUtil::getRequiredFileInput("coverimage", $sessionErrorMessageVariable, $redirectUrl), ENT_QUOTES);
        
        // Check if uploaded file is a zip file
        if(!FormUtil::isFormFileOfType($fileInputName, array("zip"))){
            sendErrorMessageAndExit("<p><b>Form error:</b> The uploaded file has to be a zip-file f.e. project.zip</p>", $sessionErrorMessageVariable);
        }

        // Check if uploaded image has correct format file
        if(!FormUtil::isFormFileOfType($imageInputName, array("jpg","png","jpeg","gif"))){
            sendErrorMessageAndExit("<p><b>Form error:</b> The uploaded cover image has to be a jpg, png, jpeg or gif f.e. cover-image.png</p>", $sessionErrorMessageVariable);
        }

        // Check if project name is already taken
        if(count($projectRepository->getProjectByName($projectName)["data"]) > 0) {
            sendErrorMessageAndExit("<p><b>Form error:</b> Project name is already taken</p>", $sessionErrorMessageVariable);
        }

        // Get username
        if(!empty($_SESSION["username"])) {
            $username = $_SESSION["username"];
        } else if(!empty($_COOKIE["username"])) {
            $username = $_COOKIE["username"];
        } else {
            // No username exists, need to login again
            Util::redirect("login.php");
        }

        // Store project information in the database
        $project = $projectRepository->insertProject($username, $projectName, $description, $catchphrase);
        $projectId = $project["lastid"];
        if($project["resultcode"] == -1){
            // Failed, delete data and send error message
            if($projectId != ""){
                $projectRepository->deleteProject($projectId);
            }
            sendErrorMessageAndExit("<p><b>Error:</b> Could not store project in database.</p>", $sessionErrorMessageVariable);
        }

        // Directory structure
        $usersDirectory = "./users";
        $userDirectory = $usersDirectory . "/" . $username;
        $projectsDirectory = $userDirectory . "/projects";
        
        $projectDirectory = $projectsDirectory . "/" . $projectId;
        $imageDirectory = $userDirectory . "/images/" . $projectId;

        // Create project directory or exit on failure
        if(!FileUtil::doesDirectoryExist($projectDirectory)) {
            if(!FileUtil::makeDirectory($projectDirectory, true)){
                // Failed, delete data and send error message
                $projectRepository->deleteProject($projectId);
                sendErrorMessageAndExit("<p><b>Error 1:</b> Something went wrong, try again or contact support.</p>", $sessionErrorMessageVariable);
            }
        }

        // Create images directory or exit on failure
        if(!FileUtil::doesDirectoryExist($imageDirectory)) {
            if(!FileUtil::makeDirectory($imageDirectory, true)){
                // Failed, delete data and send error message
                $projectRepository->deleteProject($projectId);
                sendErrorMessageAndExit("<p><b>Error 2:</b> Something went wrong, try again or contact support.</p>", $sessionErrorMessageVariable);
            }
        }

        // Save file to project directory
        $fullFilePath = FormUtil::saveFormFile($fileInputName, $projectDirectory, "");

        // Unzip the saved file
        if($fullFilePath == "" || !FileUtil::unzipFile($fullFilePath, $projectDirectory)) {
            // Failed, delete data and send error message
            $projectRepository->deleteProject($projectId);
            FileUtil::deleteDirectoryAndContent($projectDirectory);
            FileUtil::deleteDirectoryAndContent($imageDirectory);
            sendErrorMessageAndExit("<p><b>Error:</b> Something was wrong with uploaded zip file.</p>", $sessionErrorMessageVariable);
        }

        // Save project cover image to images directory
        FormUtil::saveFormFile($imageInputName, $imageDirectory, "cover-image");

        // Check if zip contains index.html
        $requiredProjectFilePath = $projectDirectory . "/index.html";
        if(!FileUtil::doesFileExist($requiredProjectFilePath)) {
            // Failed, delete data and send error message
            $projectRepository->deleteProject($projectId);
            FileUtil::deleteDirectoryAndContent($projectDirectory);
            FileUtil::deleteDirectoryAndContent($imageDirectory);
            sendErrorMessageAndExit("<p><b>Error:</b> Zip does not contain the required index.html file.</p>", $sessionErrorMessageVariable);
        }

        // Remove zip file
        if(!FileUtil::deleteFile($fullFilePath)) {
            // Do nothing, this is not a critical task
        }

        // Change privileges of files so they can be executed
        FileUtil::chmodRecursive($projectDirectory);

        $_SESSION[$sessionErrorMessageVariable] = "";

        // Send user to project page
        Util::redirect("projectpage.php?projectid=$projectId");
    }

?>