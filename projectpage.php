<?php
    require_once "startsession.php";
    require_once "authenticateSession.php";
    require_once "loadproject.php";

    $isProjectOwner = false;
    // Authenticate project ownership
    if($project != "" && $isLoggedIn) {
        // Check if the logged in user is the same as the project creator
        if($loggedInWithSession && $_SESSION["username"] != $project[0]["USERNAME"]) {
            $isProjectOwner = false;
        } else if($loggedInWithCookie && $_COOKIE["username"] != $project[0]["USERNAME"]) {
            $isProjectOwner = false;
        } else {
            // User is verified, continue to edit project
            $isProjectOwner = true;
        }
    }

?>
<!doctype html>
<html lang="se">
  <head>
    <meta charset="utf-8">
    <title>Project page</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/projectpage.css">
    <link rel="stylesheet" href="css/userpage.css">
    <script type="text/javascript" src="js/util.js"></script>
    <script type="text/javascript" src="js/commentloader.js"></script>
  </head>
  <body>
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <a class="header-item" href="index.php">Home</a>
            </div>
            <div class="header-right">
            <?php if(!$isLoggedIn): ?>
                <a class="header-item" href="login.php">Log in</a>
                <a class="header-item" href="register.php">Sign up</a>
            <?php else: ?>
                <a class="header-item userbtn" href="userpage.php">
                    <?php 
                        if(isset($_COOKIE["username"])){
                            echo $_COOKIE["username"]; 
                        } else if(isset($_SESSION["username"])){
                            echo $_SESSION["username"]; 
                        }
                    ?>
                </a>
                <a class="header-item" href="logout.php">Log out</a>
            <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if($isProjectOwner): ?>
        <div class="delete-overlay" id="delete-overlay" style="display: none;" onclick="inactivateDeleteOverlay()">
            <div class="delete-overlay-window">
                <b style="font-size: 18pt;">Delete project?</b>
                <br>
                <div class="delete-overlay-buttons">
                    <a class="btn delete-btn option-btn" style="margin-right: 5px;" href="deleteproject.php?projectid=<?= $project[0]["ID"] ?>">Delete</a>
                    <button class="btn option-btn" onclick="inactivateDeleteOverlay()">Cancel</button>
                </div>
            </div>
        </div>
        <div class="project-owner-panel">
            <b style="font-size: 16pt;">Owner panel</b>
            <a class="btn edit-btn" href="editproject.php?projectid=<?= $project[0]["ID"] ?>">Edit project</a>
            <a class="btn delete-btn" onclick="activateDeleteOverlay()">Delete project</a>
        </div>
    <?php endif; ?>
    <div class="body-content">
        <div class="project-content">
            <div class="game-frame">
                <div class="iframe-overlay" id="iframe-overlay">
                    <button class="btn game-btn" onclick="runGame()">Run game</button>
                </div>
                <iframe class="iframe" id="game-window" mozallowfullscreen="true" allowfullscreen="true" webkitallowfullscreen="true" allowtransparency="true"
                    msallowfullscreen="true" scrolling="no" frameborder="0" 
                    allow="autoplay; fullscreen *; geolocation; microphone; camera; midi; monetization; xr-spatial-tracking; gamepad; gyroscope; accelerometer; xr"
                    src="about:blank" 
                    data-src=
                    "<?php 
                        echo $projectUrl;
                    ?>">
                </iframe>
                <div class="fullscreen-btn" onclick="iframeFullscreen()"></div>
            </div>
            <div class="game-readme">
                <div class="game-name">
                    <h1>
                        <?php 
                            echo $project[0]["PROJECTNAME"];
                        ?>
                    </h1>
                </div>
                <div class="game-description">
                    <p>
                        <?php 
                            echo $project[0]["DESCRIPTION"];
                        ?>
                    </p>
                </div>
                <div class="game-credits">
                    <h2>Credits:</h2>
                    <p>
                        <?php 
                            echo "Created by: " . $project[0]["USERNAME"];
                        ?>
                    </p>
                </div>
            </div>
            <div id="comment-section">
                <h2>Comments:</h2>
                <div class="create-comment">
                    <form class="comment-form" method="POST" onsubmit="sendForm(this, 'comment')">
                        <input type="hidden" id="hidden-projectid" name="projectid" value=""/>
                        <textarea required name="comment" class="comment-write-area" rows="4" cols="40" placeholder="Required"></textarea>
                        <input class="cbtn comment-btn" type="submit" name="post-comment" value="Post comment"/>
                    </form>
                </div>
                <div id="comment-thread">
                </div>
                <button class="load-comments-btn cbtn" onclick="loadMoreComments()">Load more comments</button>
            </div>
        </div>
    </div>
    <div style="height:100%;"></div>
    <div id="footer">
        <p>Game space</p>
    </div>
  </body>
</html>