<?php

    class Util {

        // Create a safe random token of length tokenLength
        public static function getRandomToken($tokenLength){
            $token = openssl_random_pseudo_bytes($tokenLength);
            return bin2hex($token);
        }

        // Redirects to a location
        public static function redirect($location){
            header("Location: " . $location, true);
            exit();
        }

        // Clears authentication cookies
        public static function clearCookieAuthentication(){
            self::removeCookie("username");
            self::removeCookie("random_password");
            self::removeCookie("random_selector");
        }

        // Clears a cookie with name cookieName
        public static function removeCookie($cookieName){
            if(isset($_COOKIE[$cookieName])) {
                setcookie($cookieName, "", time() - 3600); 
                unset($_COOKIE[$cookieName]);
            }
        }
    }

?>