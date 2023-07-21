<?php
    require_once "DatabaseSession.php";
    
    class AuthRepository {

        // Get a non expired token for user
        public function getTokenByUsername($username, $isExpired){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $sql = "SELECT * FROM AUTH_TOKEN WHERE USERNAME = ? AND IS_EXPIRED = ? ;";
            $result = $dbSession->select($sql, array($username, $isExpired), "si");
            $dbSession->closeConnection();
            return $result;
        }

        // Mark a token as expired
        public function markAsExpired($tokenId){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $sql = "UPDATE AUTH_TOKEN SET IS_EXPIRED = ? WHERE ID = ? ;";
            $result = $dbSession->update($sql, array(1, $tokenId), "ii");
            $dbSession->closeConnection();
            return $result;
        }

        // Insert a token new token into database
        public function insertUserToken($username, $passwordHash, $selectorHash, $expiryDate){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $sql = $dbSession->buildInsertQuery("AUTH_TOKEN", array("USERNAME", "PASSWORD_HASH", "SELECTOR_HASH", "EXPIRY_DATE"));
            $result = $dbSession->insert($sql, array($username, $passwordHash, $selectorHash, $expiryDate), "ssss");
            $dbSession->closeConnection();
            return $result;
        }
    }

?>