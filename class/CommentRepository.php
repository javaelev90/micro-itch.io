<?php
    require_once "DatabaseSession.php";
    
    class CommentRepository {

        // Retrieve a certain number of comments and their replies 
        public function getCommentInterval($projectid, $min, $max){
            $sql = "SELECT c.*
            FROM (
                SELECT * 
                FROM PROJECTCOMMENT 
                WHERE PROJECTID = ? AND ROOTID IS NULL
                ORDER BY CREATIONDATE DESC LIMIT ?, ?
            ) as c
            UNION ALL
            SELECT c2.*
            FROM (
                SELECT *
                FROM PROJECTCOMMENT 
                WHERE PROJECTID = ? AND ROOTID IS NULL
                ORDER BY CREATIONDATE DESC LIMIT ?, ?
            ) as c
            INNER JOIN PROJECTCOMMENT c2 ON c2.ROOTID = c.ID
            ORDER BY CREATIONDATE DESC;";

            return $this->select($sql, array($projectid, $min, $max, $projectid, $min, $max), "iiiiii");
        }

        // Get a comment
        public function getComment($commentId, $projectId){
            $sql = "SELECT * FROM PROJECTCOMMENT WHERE ID = ? AND PROJECTID = ? ;";
            return $this->select($sql, array($commentId, $projectId), "ii");
        }

        // Insert a regular comment
        public function insertComment($username, $projectid, $comment){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $sql = $dbSession->buildInsertQuery("PROJECTCOMMENT", array("USERNAME", "PROJECTID", "COMMENT"));
            $result = $dbSession->insert($sql, array($username, $projectid, $comment), "sis");
            $dbSession->closeConnection();
            return $result;
        }

        // Insert a reply comment 
        public function insertReplyComment($username, $projectid, $rootid, $parentid, $comment){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $sql = $dbSession->buildInsertQuery("PROJECTCOMMENT", array("USERNAME", "PROJECTID", "ROOTID", "PARENTID", "COMMENT"));
            $result = $dbSession->insert($sql, array($username, $projectid, $rootid, $parentid, $comment), "siiis");
            $dbSession->closeConnection();
            return $result;
        }

        // Delete a comment
        public function deleteComment($commentId){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            try{
                $dbSession->beginTransaction();
                $sql = "DELETE FROM PROJECTCOMMENT WHERE ID = ? ;";
                $sqlRoot = "DELETE FROM PROJECTCOMMENT WHERE PARENTID = ? ;";
                $result = $dbSession->update($sql, array($commentId), "i");
                $result = $dbSession->update($sqlRoot, array($commentId), "i");
                $dbSession->commitTransaction();
            } catch(Exception $e) {
                $dbSession->rollbackTransaction();
                return $result;
            } finally {
                $dbSession->closeConnection();
            }
            return $result;
        }

        // Delete comments for a certain project
        public function deleteCommentsForProject($projectId){
            $sql = "DELETE FROM PROJECTCOMMENT WHERE PROJECTID = ? ;";
            return $this->update($sql, array($projectId), "i");
        }

        // basic select function used in class
        private function select($sql, $valueArray, $valueTypes){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $result = $dbSession->select($sql, $valueArray, $valueTypes);
            $dbSession->closeConnection();
            return $result;
        }

        // basic update function used in class
        private function update($sql, $valueArray, $valueTypes){
            $dbSession = new DatabaseSession();
            $dbSession->openConnection();
            $result = $dbSession->update($sql, $valueArray, $valueTypes);
            $dbSession->closeConnection();
            return $result;
        }
    }

?>