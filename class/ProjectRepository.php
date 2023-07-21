<?php
    require_once "DatabaseSession.php";
    
    class ProjectRepository {

        // Get projects connected to a user
        public function getProjectByUsername($username){
            $sql = "SELECT * FROM PROJECT WHERE USERNAME = ? ;";
            return $this->select($sql, array($username), "s");
        }

        // Get project with projectname
        public function getProjectByName($projectname){
            $sql = "SELECT * FROM PROJECT WHERE PROJECTNAME = ? ;";
            return $this->select($sql, array($projectname), "s");
        }

        // Get the project with projectId
        public function getProjectById($projectId){
            $sql = "SELECT * FROM PROJECT WHERE ID = ? ;";
            return $this->select($sql, array($projectId), "i");
        }

        // Get a certain number of projects
        public function getNumberOfProjects($numberOfProjects){
            $sql = "SELECT * FROM PROJECT LIMIT ? ;";
            return $this->select($sql, array($numberOfProjects), "i");
        }

        // Creates a new project post in the database
        public function insertProject($username, $projectname, $description, $catchphrase){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $sql = $dbSession->buildInsertQuery("PROJECT", array("USERNAME", "PROJECTNAME", "CATCHPHRASE", "DESCRIPTION"));
            $result = $dbSession->insert($sql, array($username, $projectname, $catchphrase, $description), "ssss");
            $dbSession->closeConnection();
            return $result;
        }

        // Deletes the project with projectid
        public function deleteProject($projectId){
            $sql = "DELETE FROM PROJECT WHERE ID = ? ;";
            return $this->update($sql, array($projectId), "i");
        }

        // Update the project with the set projectid
        public function updateProject($projectId, $projectname, $description, $catchphrase){
            $sql = "UPDATE PROJECT SET PROJECTNAME = ?, DESCRIPTION = ?, CATCHPHRASE = ? WHERE ID = ? ;";
            return $this->update($sql, array($projectname, $description, $catchphrase, $projectId), "sssi");
        }

        // Performs a regular sql select
        private function select($sql, $valueArray, $valueTypes){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $result = $dbSession->select($sql, $valueArray, $valueTypes);
            $dbSession->closeConnection();
            return $result;
        }

        // Performs a regular sql update/delete
        private function update($sql, $valueArray, $valueTypes){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $result = $dbSession->update($sql, $valueArray, $valueTypes);
            $dbSession->closeConnection();
            return $result;
        }
    }

?>