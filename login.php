<?php
    require_once "startsession.php";
    
    require_once "class/UserRepository.php"; 
    require_once "class/Util.php"; 
    require_once "class/AuthRepository.php"; 

    // Checks if the form data is set and returns it, if not it will send an error message to the user
    function getRequiredFormInput($inputName){
        if(!isset($_POST[$inputName]) || empty($_POST[$inputName])){
            $errorMsg = "<p><b>Form error:</b> $inputName can't be empty</p>";
            sendErrorMessage($errorMsg, "login.html");
            exit();
        } else {
            return $_POST[$inputName];
        }
    }

    // Checks if the string is alpha-numeric and returns it, if not it will send an error message to the user
    function checkIfAlphaNumeric($stringValue){
        if(!ctype_alnum($stringValue)){
            $errorMsg = "<p><b>Form error:</b> Username has to be alphanumeric</p>";
            sendErrorMessage($errorMsg, "login.html");
            exit();
        }
        return $stringValue;
    }

    // Sends error message to the user
    function sendErrorMessage($errorMsg, $htmlFile){
        $html = file_get_contents($htmlFile);
        $html = str_replace("<!---ERRORMSG--->", $errorMsg, $html);
        echo $html;
    }

    // Use to check if logged in
    require_once "authenticateSession.php";

    $userRepository = new UserRepository();
    $util = new Util();
    $authRepository = new AuthRepository();

    // Checks if the user is already logged in based on the authenticateSession.php script
    if($isLoggedIn){
        $util->redirect("index.php");
    }

    if(!empty($_POST["login"])){

        // Verifies the input data
        $username = checkIfAlphaNumeric(getRequiredFormInput("username"));
        $password = getRequiredFormInput("password");
        $authenticated = false;

        $user = $userRepository->getUserByUsername($username, true)["data"];

        // Checks if there is a user with that name and that the password is correct
        if($user != "" && count($user) > 0 && password_verify($password, $user[0]["PASSWORD"])){
            $authenticated = true;
        } 

        if($authenticated){
            // If the user is authenticated the session data will be set with an expiry time of 10 minutes
            $time = strtotime(date("Y-m-d H:i:s") . "+ 15 minutes");
            $_SESSION["user_id"] = $user[0]["ID"];
            $_SESSION["username"] = $user[0]["USERNAME"];
            $_SESSION["expirytime"] = $time;

            // If the user set remember me in the login form, some authentication cookies should be created
            if(!empty($_POST["remember"])){
                setcookie("username", $user[0]["USERNAME"], time() + $cookie_expiration_time);

                //Create two long random strings(random_password and random_selector) which will be used 
                //to authenticate the user via cookies

                $randomPassword = $util->getRandomToken(24);
                setcookie("random_password", $randomPassword, time() + $cookie_expiration_time);

                $randomSelector = $util->getRandomToken(32);
                setcookie("random_selector", $randomSelector, time() + $cookie_expiration_time);

                // Since storing the random string in clear text in the database is a security risk,
                // the strings are hashed before insert
                $randomPasswordHash = password_hash($randomPassword, PASSWORD_DEFAULT);
                $randomSelectorHash = password_hash($randomSelector, PASSWORD_DEFAULT);

                $expiryDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . "+ 7 days"));
                $userToken = $authRepository->getTokenByUsername($username, 0)["data"];
                
                // If the user already has an active authentication token it should be removed,
                // this could be the case if the user cleared its cookies on the client side
                if (!empty($userToken[0]["ID"])) {
                    $authRepository->markAsExpired($userToken[0]["ID"]);
                }
                // Store the new token
                $authRepository->insertUserToken($username, $randomPasswordHash, $randomSelectorHash, $expiryDate);
            } else {
                $util->clearCookieAuthentication();
            }
            $util->redirect("index.php");
        }
        // If the user tried to login with a non-existing user or supplied the wrong user they will receive an error message
        else {
            $errorMsg = "<p><b>Form error:</b> Failed to log in</p>";
            sendErrorMessage($errorMsg, "login.html");
            exit();
        }
    } else {
        echo file_get_contents("login.html");
        exit();
    }

?>