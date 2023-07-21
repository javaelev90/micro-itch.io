<?php
    require_once "startsession.php";
    require_once "authenticateSession.php";
    require_once "class/Util.php"; 
    require_once "loadproject.php"; 

    // Checks if user is logged in based on authenticateSession.php script
    if(!$isLoggedIn) {
        Util::redirect("login.php");
    }

    // Authenticate project ownership
    if($project != "") {
        if($loggedInWithSession && $_SESSION["username"] != $project[0]["USERNAME"]) {
            Util::redirect("index.php");
        } else if($loggedInWithCookie && $_COOKIE["username"] != $project[0]["USERNAME"]) {
            Util::redirect("index.php");
        } else {
            // User is verified, continue to edit project
        }
    } else {
        Util::redirect("index.php");
    }
?>
<!doctype html>
<html lang="se">
  <head>
    <meta charset="utf-8">
    <title>Edit project</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/userpage.css">
    <script src="js/util.js"></script>
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
    <div class="body-content">
        <div class="create-project-form">
            <h2>Edit project</h2>
            <form method="post" enctype="multipart/form-data" action="saveeditproject.php" id="project">
                <p>
                    <label for="projectname"><b>Project name:</b></label><br>
                    <input type="text" size="53" id="projectname" name="projectname" placeholder="Project name" 
                    value="<?php echo $project[0]["PROJECTNAME"]; ?>"/>
                </p>
                <p>
                    <label for="catchphrase"><b>Catch phrase:</b></label><br>
                    <input type="text" size="53" id="catchphrase" name="catchphrase" placeholder="Catch phrase" 
                    value="<?php echo $project[0]["CATCHPHRASE"]; ?>"/>
                </p>
                <div style="display: flex; flex-direction: row;">
                    <div style="display: flex; flex-direction: column;">
                        <label for="description"><b>Description:</b></label>
                        <textarea maxlength="4000" id="description" style="resize: none;"
                            rows="35" cols="55" name="description" form="project" 
                            placeholder="Enter project description here..." ><?php echo $project[0]["DESCRIPTION"]; ?></textarea>
                    </div>
                    <div style="display: flex; flex-direction: column; margin-left: 15px;">
                        <p>
                            <label for="coverimagefile"><b style="font-size: 16pt; color: #606474;">
                                <img id="cover-image-preview" style="cursor: pointer;" width="250px" height="200px" src="<?= $image[0]; ?>">
                                <br>
                                Cover image:</b>
                            </label>
                            <br>
                            <input style="cursor: pointer;" id="coverimagefile" onchange="loadImage(this);" type="file" name="coverimage" value="Upload image" />
                            <br>
                            <br>
                            <b>Cover image file:</b><br>
                            shown on home page in 5:4 format. <br>
                            <b>Allowed formats:</b><br>
                            png, jpg, jpeg, gif.
                        </p>
                        <p>
                            <label for="zipfile"><b style="font-size: 16pt; color: #606474;">Project file:</b></label>
                            <br>
                            <input style="cursor: pointer;" id="zipfile" type="file" name="projectfile" value="Upload project" />
                            <br>
                            <br>
                            <b>Zip-file of project:</b><br> 
                            Needs to contain a index.html to work.<br>
                            <b>Tip for fullscreen to work:</b><br>
                            Make sure the game in the index.html streches the whole viewport,<br>
                            f.e. add this the game html tag and its parent tags: <i>style="width: 100vw; height: 100vh;"</i></br>
                        </p>
                    </div>
                </div>
                <br>
                <input type="hidden" style="display: none;" name="projectid" value="<?= $project[0]["ID"]; ?>" />
                <p>
                    <input class="btn" type="submit" name="create" value="Save project"/>
                </p>
            </form> 
            <?php
                if(!empty($_SESSION["edit-project-error-msg"])){
                    echo $_SESSION["edit-project-error-msg"];
                } 
                $_SESSION["edit-project-error-msg"] = "";
            ?>
        </div>
    </div>
    <div style="height:100%;"></div>
    <div id="footer">
        <p>Game space</p>
    </div>
  </body>
</html>