<?php

    require_once "class/Util.php";
    require_once "class/FileUtil.php";

    class FormUtil {

        // Returns a form input value or exits with error message if it does not exist
        public static function getRequiredFormInput($inputName, $sessionErrorMsgVariable, $redirectUrl){
            if(!isset($_POST[$inputName]) || empty(trim($_POST[$inputName]))){
                $_SESSION[$sessionErrorMsgVariable] = "<p><b>Form error:</b> $inputName can't be empty</p>";
                Util::redirect($redirectUrl);
            } else {
                return trim($_POST[$inputName]);
            }
        }

        // Returns form input if exits, else an empty string
        public static function getFormInput($inputName){
            if(isset($_POST[$inputName]) && !empty($_POST[$inputName])){
                return trim($_POST[$inputName]);
            } else {
                return "";
            }
        }
        
        // Checks if the inputString is shorter than the maxLength
        public static function verifyInputLength($inputString, $maxLength, $inputName, $sessionErrorMsgVariable, $redirectUrl){
            if(strlen($inputString) > $maxLength) {
                $_SESSION[$sessionErrorMsgVariable] = "<p><b>Form error:</b> " . $inputName . " can only be " . $maxLength . " long</p>";
                Util::redirect($redirectUrl);
            }
            return $inputString;
        }
    
        // Check if the stringValue is alphanumeric
        public static function checkIfAlphaNumeric($stringValue, $sessionErrorMsgVariable, $redirectUrl){
            if(!ctype_alnum($stringValue)){
                $_SESSION[$sessionErrorMsgVariable] = "<p><b>Form error:</b> Username has to be alphanumeric</p>";
                Util::redirect($redirectUrl);
            }
            return $stringValue;
        }
    
        // Send an error message in an html file
        public static function sendErrorMessage($errorMsg, $htmlFile){
            $html = file_get_contents($htmlFile);
            $html = str_replace("<!---ERRORMSG--->", $errorMsg, $html);
            echo $html;
        }

        // Returns a form file input name or exits with error message if it does not exist
        public static function getRequiredFileInput($inputName, $sessionErrorMsgVariable, $redirectUrl){
            if(!isset($_FILES[$inputName]) && !is_uploaded_file($_FILES[$inputName]["tmp_name"])) {
                $_SESSION[$sessionErrorMsgVariable] = "<p><b>Form error:</b> You need to upload a zipped project.</p>";
                Util::redirect($redirectUrl);
            }
            return $inputName;
        }

        public static function getFileInput($inputName){
            if(!isset($_FILES[$inputName]) || !is_uploaded_file($_FILES[$inputName]["tmp_name"])) {
                return "";
            }
            return $inputName;
        }

        // Check if the form file has a certain file extension
        public static function isFormFileOfType($fileParamName, $fileTypeExtensionArray) {
            if(isset($_FILES[$fileParamName])) {
                $fileName = basename($_FILES[$fileParamName]["name"]);
                return FileUtil::isFileOfType($fileName, $fileTypeExtensionArray);
            }
            return false;
        }

        // Saves a form file and renames it to newFileName if that parameter is set
        public static function saveFormFile($fileParamName, $directoryPath, $newFileName) {
            // Move post file to folder
            if(isset($_FILES[$fileParamName])) {
                $newFileName = $newFileName != "" ? ($newFileName . "." . pathinfo($_FILES[$fileParamName]["name"], PATHINFO_EXTENSION)) : $_FILES[$fileParamName]["name"];
                $filePath = $directoryPath . "/" . $newFileName;
                move_uploaded_file($_FILES[$fileParamName]["tmp_name"], $filePath);
                return $filePath;
            }
            return "";
        }

    }

?>