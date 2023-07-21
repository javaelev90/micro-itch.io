<?php
    require_once "startsession.php";
    require_once "authenticateSession.php";
    
    require_once "class/Util.php"; 
    require_once "class/FileUtil.php";
    require_once "class/FormUtil.php";
    require_once "class/ProjectRepository.php";  

    function sendErrorMessageAndExit($errorMsg, $sessionErrorMessageVariable, $redirectUrl){
        $_SESSION[$sessionErrorMessageVariable] = $errorMsg;
        Util::redirect($redirectUrl);
    }
    
    // Checks if user is logged in based on authenticateSession.php script
    if(!$isLoggedIn) {
        Util::redirect("login.php");
    }

    if(!empty($_POST["projectid"])) {

        // STEPS INVOLVED IN THE EDIT PROCESS
        // check if project id is supplied
        // get project
        // verify ownership of project
        // get all supplied form data
        // if new files
        //      delete old files
        //      save new files
        // if new field data
        //      update project row with new data

        $projectRepository = new ProjectRepository();
        $projectid = $_POST["projectid"];

        $project = $projectRepository->getProjectById($projectid)["data"];

        if(count($project) > 0) {
            // Authenticate project ownership
            if($project != "") {
                // Check if the logged in user is the same as the project creator
                if($loggedInWithSession && $_SESSION["username"] != $project[0]["USERNAME"]) {
                    Util::redirect("index.php");
                } else if($loggedInWithCookie && $_COOKIE["username"] != $project[0]["USERNAME"]) {
                    Util::redirect("index.php");
                } else {
                    // User is verified, continue to edit project
                }
            } else {
                Util::redirect("index.php");
            }
        } else {
            Util::redirect("index.php");
        }


        $sessionErrorMessageVariable = "edit-project-error-msg";
        $redirectUrl = "editproject.php?projectid=$projectid";

        // Verify and retrieve the form inputs
        $newProjectName = htmlspecialchars(FormUtil::verifyInputLength(FormUtil::getFormInput("projectname", $sessionErrorMessageVariable, $redirectUrl), 254, "project name", $sessionErrorMessageVariable, $redirectUrl), ENT_QUOTES);
        $newCatchphrase = htmlspecialchars(FormUtil::verifyInputLength(FormUtil::getFormInput("catchphrase"), 80, "catch phrase",$sessionErrorMessageVariable, $redirectUrl), ENT_QUOTES);
        $newDescription = htmlspecialchars(FormUtil::verifyInputLength(FormUtil::getFormInput("description", $sessionErrorMessageVariable, $redirectUrl), 4000, "description", $sessionErrorMessageVariable, $redirectUrl), ENT_QUOTES);
        $fileInputName = htmlspecialchars(FormUtil::getFileInput("projectfile"), ENT_QUOTES);
        $imageInputName = htmlspecialchars(FormUtil::getFileInput("coverimage"), ENT_QUOTES);

        $oldProjectName = $project[0]["PROJECTNAME"];
        $oldCatchphrase = $project[0]["CATCHPHRASE"];
        $oldDescription = $project[0]["DESCRIPTION"];

        // Check if project name is already taken
        if($newProjectName != $oldProjectName && count($projectRepository->getProjectByName($newProjectName)["data"]) > 0) {
            sendErrorMessageAndExit("<p><b>Form error:</b> Project name is already taken</p>", $sessionErrorMessageVariable, $redirectUrl);
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

        // Directory structure
        $usersDirectory = "./users";
        $userDirectory = $usersDirectory . "/" . $username;
        $projectsDirectory = $userDirectory . "/projects";
        
        $projectDirectory = $projectsDirectory . "/" . $projectid;
        $imageDirectory = $userDirectory . "/images/" . $projectid;

        // Set the values to update
        if($newProjectName != "" && $newProjectName != $oldProjectName){
            $oldProjectName = $newProjectName;
        }
        if($newCatchphrase != "" && $newCatchphrase != $oldCatchphrase){
            $oldCatchphrase = $newCatchphrase;
        }
        if($newDescription != "" && $newDescription != $oldDescription){
            $oldDescription = $newDescription;
        }

        // Check if uploaded file is a zip file
        if($fileInputName != ""){
            if(!FormUtil::isFormFileOfType($fileInputName, array("zip"))){
                sendErrorMessageAndExit("<p><b>Form error:</b> The uploaded file has to be a zip-file f.e. project.zip</p>", $sessionErrorMessageVariable, $redirectUrl);
            }
        }
        // Check if uploaded image has correct format file
        if($imageInputName != ""){
            if(!FormUtil::isFormFileOfType($imageInputName, array("jpg","png","jpeg","gif"))){
                sendErrorMessageAndExit("<p><b>Form error:</b> The uploaded cover image has to be a jpg, png, jpeg or gif f.e. cover-image.png</p>", $sessionErrorMessageVariable, $redirectUrl);
            }
        }
        
        // Update project file
        if($fileInputName != ""){
            FileUtil::deleteDirectoryContent($projectDirectory);

            // Save file to project directory
            $fullFilePath = FormUtil::saveFormFile($fileInputName, $projectDirectory, "");

            // Unzip the saved file
            if($fullFilePath == "" || !FileUtil::unzipFile($fullFilePath, $projectDirectory)) {
                FileUtil::deleteDirectoryContent($projectDirectory);
                sendErrorMessageAndExit("<p><b>Error:</b> Something was wrong with uploaded zip file.</p>", $sessionErrorMessageVariable, $redirectUrl);
            }

            // Check if zip contains index.html
            $requiredProjectFilePath = $projectDirectory . "/index.html";
            if(!FileUtil::doesFileExist($requiredProjectFilePath)) {
                FileUtil::deleteDirectoryContent($projectDirectory);
                sendErrorMessageAndExit("<p><b>Error:</b> Zip does not contain the required index.html file.</p>", $sessionErrorMessageVariable, $redirectUrl);
            }

            // Remove zip file
            if(!FileUtil::deleteFile($fullFilePath)) {
                // Do nothing, this is not a critical task
            }

            // Change privileges of files so they can be executed
            FileUtil::chmodRecursive($projectDirectory);

        }

        // Update cover image file
        if($imageInputName != ""){
            FileUtil::deleteDirectoryContent($imageDirectory);
            
            // Save project cover image to images directory
            FormUtil::saveFormFile($imageInputName, $imageDirectory, "cover-image"); 
        }


        // Update project information in the database
        if($projectRepository->updateProject($projectid, $oldProjectName, $oldDescription, $oldCatchphrase)["resultcode"] == -1){
            sendErrorMessageAndExit("<p><b>Error:</b> Could not update project in database.</p>", $sessionErrorMessageVariable, $redirectUrl);
        }

        $_SESSION[$sessionErrorMessageVariable] = "";

        // Send user to project page
        Util::redirect("projectpage.php?projectid=$projectid");
    }

?>