<?php
    require_once "DatabaseSession.php";
    
    class UserRepository {

        // Gets a user based on username, can also set if the search should be case sensitive
        public function getUserByUsername($username, $caseSensitive){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            if($caseSensitive){
                $sql = "SELECT * FROM USER WHERE BINARY USERNAME = ? ;";
            } else {
                $sql = "SELECT * FROM USER WHERE USERNAME = ? ;";
            }
            $result = $dbSession->select($sql, array($username), "s");
            $dbSession->closeConnection();
            return $result;
        }

        // Insert a new user
        public function insertUser($username, $password, $email){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $sql = $dbSession->buildInsertQuery("USER", array("USERNAME", "PASSWORD", "EMAIL"));
            $result = $dbSession->insert($sql, array($username, $password, $email), "sss");
            $dbSession->closeConnection();
            return $result;
        }

        public function deleteUser(){
        }

        public function updateUser(){
        }
    }

?>