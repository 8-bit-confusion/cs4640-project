<!--
    OpenLearn — Sprint 2 (Static)
    Authors: Natalie Nguyen, Lilli Hrncir, Lily Wasko
    Course: CS 4640 (Fall 2025)
    Deployed URL: https://cs4640.cs.virginia.edu/gzg8pf/project-static/
-->
    
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Register - OpenLearn</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <meta name="author" content="Lilli Hrncir">
        <link rel="stylesheet" href="styles/main.css">
        <link rel="icon" type="image/png" href="styles/OpenLearnIcon.png">
    </head>
    <body class="flex-col page-fill-body" style="background-color: var(--surface);">
        <header id="main-header" class="flex-row" style="justify-content: space-between; align-items: center;">
            <a class="nav-main" href="./?command=show-welcome"><h1 style="font-weight: normal;">OpenLearn</h1></a>
        </header>
        <div class="flex-row register" style="flex-grow: 1;">
            <div class="create-account">
                <h1> Create an OpenLearn Account </h1>
                <form class="flex-col register-form" method="post">
                    <input type="hidden" name="command" value="do-register">
                    <div class="div-input">
                        <label for="username">Username</label><br>
                        <input class="register-input" type="text" id="username" name="username" required>
                    </div>
                    <div class="div-input">
                        <label for="display_name">Display name</label><br>
                        <input class="register-input" type="text" id="display_name" name="display_name" required>
                    </div>
                    <div class="div-input">
                        <label for="pwd">Password</label><br>
                        <input class="register-input" type="password" id="pwd" name="pwd" required>
                    </div>
                    <div class="div-input">
                        <label for="retype_pwd">Retype password</label><br>
                        <input class="register-input" type="password" id="retype_pwd" name="retype_pwd" required> 
                    </div>
                    <div class="div-input submit">
                        <input class="styled-button" type="submit" value="Submit"> 
                    </div>
                </form>
            </div>
            <div style="width:50%">
                <img src="styles/OpenLearnLogo.png" alt="Open Learn Logo" width="400" height="400" style="vertical-align:middle; text-align:center;">
            </div>
        </div>
    </body>
</html>