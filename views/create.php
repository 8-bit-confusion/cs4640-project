<!--
OpenLearn — Sprint 2 (Static)
Authors: Natalie Nguyen, Lilli Hrncir, Lily Wasko
Course: CS 4640 (Fall 2025)
Deployed URL: https://cs4640.cs.virginia.edu/gzg8pf/project-static/
-->

    
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Create Resource - OpenLearn</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <meta name="author" content="Lilli Hrncir">
        <link rel="stylesheet" href="styles/main.css">
    </head>
    <body class="flex-col page-fill-body">
        <header id="main-header" class="flex-row" style="justify-content: space-between; align-items: center;">
            <a class="nav-main" href="./?command=show-explore"><h1 style="font-weight: normal;">OpenLearn</h1></a>
            <nav>
                <a href="./?command=show-profile">
                    <img class="profile-picture" src="styles/pfp.jpg" alt="Profile picture">
                </a>
            </nav>
        </header>
        <div class="flex-row center" style="flex-grow: 1;">
            <div class="flex-col" style="max-height:75%;min-width: min-content;min-height: min-content;max-width: 55%;width: -webkit-fill-available;">
                <form id="create-form" class="flex-col center" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="command" value="do-create">
                    <div class="div-input">
                        <label for="resource-title">Title</label><br>
                        <input class="register-input" style="font-size:x-large;" name="title" id="resource-title" required>
                    </div>
                    <div class="div-input description">
                        <label for="resource-description">Description</label><br>
                        <textarea class="register-input" style="resize:none;" name="description" id="resource-description" rows="4"></textarea>
                    </div>
                    <input class="register-input" type="file" name="files[]" id="resource-files" multiple>
                    <textarea class="register-input comment-input" rows="2" title="tags" name="tags" id="resource-tags" placeholder="Enter tags as space separated strings with only letters and underscores; e.g. 'tag_a tag_b'"></textarea>
                    <div class="div-input submit">
                        <button class="styled-button" type="submit" name="publish" id="publish-resource">Publish Resource</button>
                    </div>
                </form>
            </div>
        </div>
        <script src="create-resource.js"></script>
    </body>
</html>