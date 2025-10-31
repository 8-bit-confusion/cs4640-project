<!--
    OpenLearn — Sprint 2 (Static)
    Authors: Natalie Nguyen, Lilli Hrncir, Lily Wasko
    Course: CS 4640 (Fall 2025)
    Deployed URL: https://cs4640.cs.virginia.edu/gzg8pf/project-static/
-->
    
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>OpenLearn</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <meta name="author" content="Lily Wasko">
        <link rel="stylesheet" href="styles/main.css">
        <link rel="stylesheet" href="styles/resource.css">
    </head>
    <body class="page-fill-body flex-col" style="padding-bottom: 0px;">
        <header id="main-header" class="flex-row" style="justify-content: space-between; align-items: center;">
            <a class="nav-main" href="./?command=show-explore"><h1 style="font-weight: normal;">OpenLearn</h1></a>
            <nav>
                <a href="./?command=show-profile" aria-label="Open profile">
                    <img class="profile-picture" src="styles/pfp.jpg" alt="Profile picture">
                </a>
            </nav>
        </header>
        <div>
            <form>
                <input type="hidden" name="target_resource" value="<?php echo $target_resource ?>">
                <input type="hidden" name="command" value="do-delete">
                <button style="background-color:red;" type="submit">Delete Resource</button>
            </form>
        </div>
        <div class="flex-row resource-view-container">
            <div class="spacing"></div>
            <div class="resource-view flex-col">
                <div class="title-bar-container flex-row">
                    <span style="font-size: 48px;"><?php echo $resource_data["title"] ?></span>
                    <div class="flex-row" style="gap: 10px;">
                        <span style="font-size: 24px;">###</span>
                        <!-- Download entire resource not yet implemented -->
                        <img class="download-icon-large" src="styles/download-icon.svg" alt="downloads">
                    </div>
                </div>
                <span>
                    <?php echo $target_resource["body"];?>
                </span>
                <img class="preview" src="styles/img-preview.jpg" alt="Image preview">
                <div class="outline-section flex-col">
                    <div class="styled-file">
                        <?php for ( $i = 0; $i < count($file_data); $i++) { ?>
                        <img style="height:20px;justify-self:left;" src="styles/attach-file-icon.png" alt="File attachment icon">
                        <span style="flex-grow: 1"><?php echo $file_data[$i][0]; ?></span>
                        <form>
                            <input type="hidden" name="command" value="do-download">
                            <input type="hidden" name="file-key" value="<?php echo $$file_data[$i][1]; ?>">
                            <button id="rv-download-button" class="flex-col icon-button" aria-label="Download file" type="button">
                                <img src="styles/download-icon.svg" alt="Download icon">
                            </button>
                        </form>
                        <?php } ?>
                    </div>
                </div>
                <div class="flex-row tags">
                <?php foreach (json_decode($target_resource["tags"]) as $tag) { ?>
                    <div class="tag flex-row">
                        <span><?php echo $tag ?></span>
                    </div>
                <?php } ?>
                </div>
            </div>
            <div class="resource-comments flex-col">
                <span style="font-size: 36px; height: 42px;">Comments</span>
                <form class="div-input flex-col" style="gap: 10px; align-items: end; padding-bottom: 10px;" method="post">
                    <input type="hidden" name="command" value="do-comment">
                    <textarea class="register-input comment-input" rows="4" name="comment" aria-label="Comment entry field" required></textarea>
                    <button class="styled-button" type="submit">Post comment</button>
                </form>
                <div class="comment-container flex-col">
                    <div class="comment flex-row">
                        <img class="comment-pfp" src="styles/pfp.jpg" alt="Commenter profile picture">
                        <span style="color: var(--on-secondary-surface);">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                        </span>
                    </div>
                    <div class="flex-row">
                        <button class="link-button">Reply</button>
                        <button class="link-button">Delete comment</button>
                    </div>
                </div>
                <div class="comment-container flex-col">
                    <div class="comment flex-row">
                        <img class="comment-pfp" src="styles/pfp.jpg" alt="Commenter profile picture">
                        <span style="color: var(--on-secondary-surface);">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                        </span>
                    </div>
                    <div class="flex-row">
                        <button class="link-button">Reply</button>
                        <button class="link-button">Delete comment</button>
                    </div>
                    <div class="comment-container flex-col">
                        <div class="comment flex-row">
                            <img class="comment-pfp" src="styles/pfp.jpg" alt="Commenter profile picture">
                            <span style="color: var(--on-secondary-surface);">
                                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </span>
                        </div>
                        <div class="flex-row">
                            <button class="link-button">Reply</button>
                            <button class="link-button">Delete comment</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="spacing"></div>
        </div>
    </body>
</html>