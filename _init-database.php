<?php
    /* !!! 
    
    This is only used to initialize database,
    it is not needed after that. Manual use.           
    
    */
    require "class/DatabaseSession.php"; 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    header("Content-type: text/plain");
    
    $createUserTableSQL = 
    "CREATE TABLE IF NOT EXISTS USER
    (   
        ID INTEGER unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        USERNAME VARCHAR(255) NOT NULL,
        PASSWORD VARCHAR(255) NOT NULL,
        EMAIL VARCHAR(255) NOT NULL,
        CREATION_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) 
    AUTO_INCREMENT=1 CHARACTER SET utf8;";
        
    $createAuthTableSQL = 
    "CREATE TABLE IF NOT EXISTS AUTH_TOKEN
    (   
        ID INTEGER unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        USERNAME VARCHAR(255) NOT NULL,
        PASSWORD_HASH VARCHAR(255) NOT NULL,
        SELECTOR_HASH VARCHAR(255) NOT NULL,
        IS_EXPIRED INTEGER NOT NULL DEFAULT 0,
        EXPIRY_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CREATION_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) 
    AUTO_INCREMENT=1 CHARACTER SET utf8;";

    $createProjectTableSQL = 
    "CREATE TABLE IF NOT EXISTS PROJECT
    (   
        ID INTEGER unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        USERNAME VARCHAR(255) NOT NULL,
        PROJECTNAME VARCHAR(255) NOT NULL,
        CATCHPHRASE VARCHAR(80),
        DESCRIPTION TEXT NOT NULL,
        CREATION_DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) 
    AUTO_INCREMENT=1 CHARACTER SET utf8;";

    $createProjectCommentsTableSQL = 
    "CREATE TABLE IF NOT EXISTS PROJECTCOMMENT
    (   
        ID INTEGER unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        PARENTID INTEGER,
        ROOTID INTEGER ,
        USERNAME VARCHAR(255) NOT NULL,
        PROJECTID INTEGER NOT NULL,
        COMMENT TEXT NOT NULL,
        CREATIONDATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) 
    AUTO_INCREMENT=1 CHARACTER SET utf8;";


    $dropPostTableSQL = "DROP TABLE USER;";
    $dropImageTableSQL = "DROP TABLE AUTH_TOKEN;";
    $dropProjectTableSQL = "DROP TABLE PROJECT;";
    $dropProjectTableSQL = "DROP TABLE PROJECTCOMMENT;";
    
    $dbm = new DatabaseSession();
    $dbm->openConnection();
    $result = $dbm->executeSql($createUserTableSQL);
    echo $result["errormsg"];
    $result = $dbm->executeSql($createAuthTableSQL);
    echo $result["errormsg"];
    $result = $dbm->executeSql($createProjectTableSQL);
    echo $result["errormsg"];
    $result = $dbm->executeSql($createProjectCommentsTableSQL);
    echo $result["errormsg"];

    $dbm->closeConnection();
?>