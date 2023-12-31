<?php
    class DatabaseSession {

        private $hostName = "host:port";
        private $userName = "user";
        private $password = "pass";
        private $databaseName = "db_name";
        
        private $db;

        public function __construct() {
        }

        public function openConnection() {
            // Open database connection
            $this->db = new mysqli( $this->hostName,
                                     $this->userName,
                                     $this->password,
                                     $this->databaseName);
            // Exit if there was a connection problem
            if($this->db->connect_error){
                return array(
                    "resultcode" => -1,
                    "errormsg" => $this->db->connect_error,
                    "data" => ""
                );
            }
            return array(
                "resultcode" => 0,
                "errormsg" => "",
                "data" => ""
            );
        }

        public function closeConnection(){
            $this->db->close();
            return array(
                "resultcode" => 0,
                "errormsg" => "",
                "data" => ""
            );
        }

        public function beginTransaction(){
            $this->db->begin_transaction();
        }

        public function commitTransaction(){
            $this->db->commit();
        }

        public function rollbackTransaction(){
            $this->db->rollback();
        }

        // Builds a select query from and array of keys
        public function buildSelectQuery($tableName, $keys){
            $queryStart = "SELECT ";
            $queryEnd   = " FROM $tableName";
            
            // Concatenate all keys into query
            for($i = 0; $i < count($keys); $i++){
                $queryStart = $queryStart . $keys[$i];
                
                // Add a comma for all keys but the last
                if($i < count($keys) - 1){
                    $queryStart = $queryStart . ",";
                }
            }
            return $queryStart . $queryEnd;
        }

        // Builds an insert query from a key array
        public function buildInsertQuery($tableName, $keys){
            $queryStart = "INSERT INTO $tableName ( ";
            $queryMiddle   = " ) VALUES ( ";
            $queryEnd   = " );";
            // Concatenate all keys into query
            for($i = 0; $i < count($keys); $i++){
                $queryStart = $queryStart . $keys[$i];
                $queryMiddle = $queryMiddle . "?";
                // Add a comma for all keys but the last
                if($i < count($keys) - 1){
                    $queryStart = $queryStart . ",";
                    $queryMiddle = $queryMiddle . ",";
                }
            }
            return $queryStart . $queryMiddle . $queryEnd;
        }

        // Executes insert sql and returns ids that were created
        public function insert($sql, $values, $valueTypes){
            $preparedStatement = $this->db->prepare($sql);
            $preparedStatement->bind_param($valueTypes, ...$values);
            $preparedStatement->execute();
            $lastId = $this->db->insert_id;
            $preparedStatement->close();
            return array(
                "resultcode" => $this->db->error == "" ? 0 : -1,
                "errormsg" => $this->db->error,
                "lastid" => $lastId
            );
        }

        // Executes update/delete sql
        public function update($sql, $values, $valueTypes){
            $preparedStatement = $this->db->prepare($sql);
            $preparedStatement->bind_param($valueTypes, ...$values);
            $preparedStatement->execute();
            $preparedStatement->close();
            return array(
                "resultcode" => $this->db->error == "" ? 0 : -1,
                "errormsg" => $this->db->error
            );
        }

        // Executes a select query in the database
        public function select($sql, $values, $valueTypes) {
            // Make a prepared statment to prevent sql injection
            $preparedStatement = $this->db->prepare($sql);
            
            // Bind params to the prepared statement
            $preparedStatement->bind_param($valueTypes, ...$values);  
            $preparedStatement->execute();

            $result = $preparedStatement->get_result(); 
            // Fetch all data
            $data = $result->fetch_all(MYSQLI_ASSOC);
            // Close the statement if the previous operations went well
            $preparedStatement->close();
            
            return array(
                "resultcode" => $this->db->error == "" ? 0 : -1,
                "errormsg" => $this->db->error,
                "data" => $data
            );
        }

        // Simple select query executer
        public function executeSql($sql){
            $result = $this->db->query($sql);
            return array(
                "resultcode" => $this->db->error == "" ? 0 : -1,
                "errormsg" => $this->db->error,
                "data" => $result
            );;
        }

    }

?>