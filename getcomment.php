<?php
    // This script should retrieve and send the following json structure
    // $response = array(
    //     {
    //         "id" => "1",
    //         "username" => "",
    //         "creationdate" => "",
    //         "comment" => "",
    //         "replies" => array({
    //                 "id" => "2",
    //                 "rootid" => "1",
    //                 "parentid" => "1",
    //                 "username" => "",
    //                 "creationdate" => "",
    //                 "comment" => ""
    //             },
    //             {
    //                 "id" => "3",
    //                 "rootid" => "1",
    //                 "parentid" => "1",
    //                 "username" => "",
    //                 "creationdate" => "",
    //                 "comment" => ""
    //             }
    //         )
    //     },
    // );

    require_once "class/CommentRepository.php";

    header('Content-Type: application/json');

    function getSet($inputName) {
        return isset($_GET[$inputName]) ? $_GET[$inputName] : "";
    }

    $response = array();

    // Checks if the needed parameters projectid, interval min, interval max are set
    if(is_numeric(getSet("projectid")) && is_numeric(getSet("min")) && is_numeric(getSet("max"))){
        $commentRepository = new CommentRepository();

        // Get a certain number of comments from the project
        $comments = $commentRepository->getCommentInterval($_GET["projectid"], $_GET["min"], $_GET["max"]);
        
        if($comments["resultcode"] != -1){
            // If there are comments
            if(count($comments["data"]) > 0){
                $rows = $comments["data"];

                // The json structure that is created below can be seen at the top of this php file
                foreach($rows as $row){
                    if(empty($row["ROOTID"])){
                        $comment = array();
                        $comment["id"] = $row["ID"];
                        $comment["rootid"] = null; 
                        $comment["parentid"] = null; 
                        $comment["username"] = $row["USERNAME"];
                        $comment["creationdate"] = $row["CREATIONDATE"];
                        $comment["comment"] = $row["COMMENT"];
                        $comment["replies"] = array();
                        
                        // Adds comment replies if there are any
                        foreach($rows as $row){
                            if(!empty($row["ROOTID"]) && $row["ROOTID"] == $comment["id"]){
                                $reply = array();
                                $reply["id"] = $row["ID"]; 
                                $reply["rootid"] = $row["ROOTID"]; 
                                $reply["parentid"] = $row["PARENTID"]; 
                                $reply["username"] = $row["USERNAME"]; 
                                $reply["comment"] = $row["COMMENT"]; 
                                $reply["creationdate"] = $row["CREATIONDATE"]; 
                                array_push($comment["replies"], $reply);
                            }
                        }
                        array_push($response, $comment);
                    }
                }
            }
        } 
    }
    echo json_encode($response);

?>