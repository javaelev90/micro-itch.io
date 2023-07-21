<?php

    require_once "class/AuthRepository.php";
    require_once "class/Util.php";

    $authRepository = new AuthRepository();

    // Expiration time is set to 1 week
    $cookie_expiration_time = 60 * 60 * 24 * 7;
    $loggedInWithSession = false;
    $loggedInWithCookie = false;

    $isLoggedIn = false;
    $currentDateTime = date("Y-m-d H:i:s");

    $time = strtotime(date("Y-m-d H:i:s"));
    // If the session has passed expirytime it should be cleared and destroyed
    if(!empty($_SESSION["expirytime"]) && $time > $_SESSION["expirytime"]){
        $_SESSION["user_id"] = "";
        $_SESSION["username"] = "";
        $_SESSION["expirytime"] = "";
        unset($_SESSION["user_id"]);
        unset($_SESSION["username"]);
        unset($_SESSION["expirytime"]);
        session_destroy();
    }

    // If session username is set it means the user is logged in
    if(!empty($_SESSION["username"]) && $_SESSION["username"] != ""){
        $isLoggedIn = true;
        $loggedInWithSession = true;
    }
    // If the user has the three required cookies (username, random_password, random_selector) then try to authenticate
    else if(!empty($_COOKIE["username"]) && !empty($_COOKIE["random_password"]) && !empty($_COOKIE["random_selector"])){
        $passwordValidated = false;
        $selectorValidated = false;
        $expiryDateValidated = false;

        // Get an active user authentication token from the database
        $userToken = $authRepository->getTokenByUsername($_COOKIE["username"], 0)["data"];
        
        // If there is no active authentication token the user is not logged in
        if(count($userToken) > 0){

            // Verifies the random password that the user supplied
            if (password_verify($_COOKIE["random_password"], $userToken[0]["PASSWORD_HASH"])) {
                $passwordValidated = true;
            }
            
            // Verifies the second random password that the user supplied
            if (password_verify($_COOKIE["random_selector"], $userToken[0]["SELECTOR_HASH"])) {
                $selectorValidated = true;
            }
            
            // Checks if the user token is expired
            if($userToken[0]["EXPIRY_DATE"] >= $currentDateTime) {
                $expiryDateValidated = true;
            }

            // If the cookie data passed all authentication checks then the user is logged in 
            if (!empty($userToken[0]["ID"]) && $passwordValidated && $selectorValidated && $expiryDateValidated) {
                $isLoggedIn = true;
                $loggedInWithCookie = true;
            } 
            // If the user cookie didn't pass the checks then mark the token as expired and clear the cookies
            else {
                if(!empty($userToken["ID"])) {
                    $authRepository->markAsExpired($userToken[0]["ID"]);
                }
                Util::clearCookieAuthentication();
            }
        }
    }


?>