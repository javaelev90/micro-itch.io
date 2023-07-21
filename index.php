<?php
    require_once "startsession.php";
    require_once "authenticateSession.php";
    require_once "loadhomepage.php";
?>
<!doctype html>
<html lang="se">
  <head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/homepage.css">
    <script type="text/javascript" src="js/util.js"></script>
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
        <div class="home-page-content">
            <h1>Games</h1>
            <div class="game-boxes">
                <?php foreach($projects as $key => $value): ?>
                    <a class="game-box" href="projectpage.php?projectid=<?= $value["ID"]; ?>">
                        <img style="background: url(<?= $projectImages[$value["ID"]]; ?>); background-size: 225px 175px;">
                        <div class="project-name"><?= $value["PROJECTNAME"]; ?></div>
                        <div class="project-catchphrase"><?= $value["CATCHPHRASE"]; ?></div>
                        <div class="project-owner"><?= $value["USERNAME"]; ?></div>
                    </a>
                <?php endforeach; ?>       
            </div>
        </div>
    </div>
    <div style="height:100%;"></div>
    <div id="footer">
        <p>Game space</p>
    </div>
  </body>
</html>