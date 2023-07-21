<?php

    require_once "class/UserRepository.php"; 
    require_once "class/Util.php"; 

    // Returns the required input value or sends and error message if something is wrong
    function getRequiredFormInput($inputName){
        if(!isset($_POST[$inputName]) || empty($_POST[$inputName])){
            $errorMsg = "<p><b>Form error:</b> $inputName can't be empty</p><br>";
            sendErrorMessage($errorMsg, "register.html");
            exit();
        } else {
            return $_POST[$inputName];
        }
    }

    // Verifies the password is secure enough and that the passwords are the same,
    // if something goes wrong the user will be supplied an error message
    function verifyPassword($password1, $password2){
        $uppercase = preg_match('@[A-Z]@', $password1);
        $lowercase = preg_match('@[a-z]@', $password1);
        $number = preg_match('@[0-9]@', $password1);
        $specialChars = preg_match('@[^\w]@', $password1);
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password1) < 8){
            $errorMsg = "<p><b>Form error:</b><br>" .
            "Password must include uppercase characters<br>" .
            "Password must include lowercase characters<br>" .
            "Password must include numbers characters<br>" .
            "Password must include special characters<br>" .
            "Password must must be atleast 8 characters long</p>";
            sendErrorMessage($errorMsg, "register.html");
            exit;
        }
        if($password1 != $password2){
            $errorMsg = "<p><b>Form error:</b><br>" .
            "Password is not equal to the repeated password</p>";
            sendErrorMessage($errorMsg, "register.html");
            exit();
        }
    }

    // Verifies the username
    function verifyUsername($username){
        checkIfAlphaNumeric($username);
        checkIfUsernameExists($username);
    }

    // Checks if string is alpha-numerical else sends an error message to user
    function checkIfAlphaNumeric($stringValue){
        if(!ctype_alnum($stringValue)){
            $errorMsg = "<p><b>Form error:</b> Username has to be alphanumeric</p>";
            sendErrorMessage($errorMsg, "register.html");
            exit();
        }
    }

    // Looks up the username in the database, if it exists an error message will
    // be sent to the user
    function checkIfUsernameExists($username){
        $userRepository = new UserRepository();
        $result = $userRepository->getUserByUsername($username, false);
        if($result["data"] != "" && count($result["data"]) > 0){
            $errorMsg = "<p><b>Form error:</b><br>" .
            "Username not available.</p>";
            sendErrorMessage($errorMsg, "register.html");
            exit();
        }
    }

    // Sends error message to user
    function sendErrorMessage($errorMsg, $htmlFile){
        $html = file_get_contents($htmlFile);
        $html = str_replace("<!---ERRORMSG--->", $errorMsg, $html);
        echo $html;
    }

    if(!empty($_POST["register"])){
        $username = htmlspecialchars(getRequiredFormInput("username"), ENT_QUOTES);
        $password1 = htmlspecialchars(getRequiredFormInput("password1"), ENT_QUOTES);
        $password2 = htmlspecialchars(getRequiredFormInput("password2"), ENT_QUOTES);
        $email = htmlspecialchars(getRequiredFormInput("email"), ENT_QUOTES);

        verifyUsername($username);
        verifyPassword($password1, $password2);

        $userRepository = new UserRepository();
        // Hashes password for security reasons before storing it in the database
        $passwordHashed = password_hash($password1, PASSWORD_DEFAULT);

        $result = $userRepository->insertUser($username, $passwordHashed, $email);
        
        // The user could not be created the user will receive an error message
        if($result["resultcode"] == -1){
            $errorMsg = "<p><b>Form error:</b><br>" .
            $result["errormsg"] . "</p>";
            sendErrorMessage($errorMsg, "register.html");
            exit();
        }
        Util::redirect("index.php");
    } else {
        $html = file_get_contents("register.html");
        $html = str_replace("<!---ERRORMSG--->", "", $html);
        echo $html;
    }


?>