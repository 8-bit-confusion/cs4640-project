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
        <div class="flex-row resource-view-container">
            <div class="spacing"></div>
            <div class="resource-view flex-col">
                <div class="title-bar-container flex-row">
                    <span style="font-size: 48px;"><?php echo $resource_data["title"]; ?></span>
                    <div class="flex-row" style="gap: 10px;">
                        <span style="font-size: 24px;"><?php echo $resource_data["download_count"]; ?></span>
                        <!-- Download entire resource not yet implemented -->
                        <img class="download-icon-large" src="styles/download-icon.svg" alt="downloads">
                    </div>
                </div>
                <span>
                    <?php echo $resource_data["body"];?>
                </span>
                <img class="preview" src="styles/img-preview.jpg" alt="Image preview">
                <div class="outline-section flex-col">
                    <?php if (count($file_data) == 0) { echo "No attached files."; } else { ?>
                        <div class="styled-file">
                            <?php for ( $i = 0; $i < count($file_data); $i++) { ?>
                            <img style="height:20px;justify-self:left;" src="styles/attach-file-icon.png" alt="File attachment icon">
                            <span style="flex-grow: 1"><?php echo $file_data[$i][0]; ?></span>
                            <form>
                                <input type="hidden" name="command" value="do-download">
                                <input type="hidden" name="file-key" value="<?php echo $file_data[$i][1]; ?>">
                                <button id="rv-download-button" class="flex-col icon-button" aria-label="Download file" type="button">
                                    <img src="styles/download-icon.svg" alt="Download icon">
                                </button>
                            </form>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="flex-row tags">
                <?php foreach ($tags as $tag) { ?>
                    <div class="tag flex-row">
                        <span><?php echo $tag ?></span>
                    </div>
                <?php } ?>
                </div>
                <?php if ($_SESSION["username"] == $resource_data["author"]) { ?>
                    <div style="align-self: end;">
                        <form>
                            <input type="hidden" name="target_resource" value="<?php echo $target_resource; ?>">
                            <input type="hidden" name="resource_author" value="<?php echo $resource_data["author"]; ?>">
                            <input type="hidden" name="command" value="do-delete">
                            <button class="styled-button" style="background-color: #b3261e;" type="submit">Delete Resource</button>
                        </form>
                    </div>
                <?php } ?>
            </div>
            <div class="resource-comments flex-col" style="overflow-y: auto;">
                <span style="font-size: 36px; height: 42px;">Comments</span>
                <?php if (!isset($this->context["replying_to"]) || $this->context["replying_to"] == NULL) { ?>
                    <form class="div-input flex-col" style="gap: 10px; align-items: end; padding-bottom: 10px;" method="post">
                        <input type="hidden" name="command" value="do-comment">
                        <input type="hidden" name="resource_id" value="<?php echo $resource_data["id"]; ?>">
                        <input type="hidden" name="parent_id" value="null">
                        <textarea class="register-input comment-input" rows="4" name="comment" aria-label="Comment entry field" required></textarea>
                        <button class="styled-button" type="submit">Post comment</button>
                    </form>
                <?php } ?>
                <?php foreach ($comments as $comment) { ?>
                    <?php if ($comment["parent_id"] == NULL) { ?>
                        <div class="comment-container flex-col">
                            <div class="comment flex-row">
                                <img class="comment-pfp" src="styles/pfp.jpg" alt="Commenter profile picture">
                                <div class="flex-col">
                                    <span style="color: var(--on-secondary-surface);"><?php echo $comment["author"]; ?></span>
                                    <span style="color: var(--on-secondary-surface);"><?php echo $comment["body"]; ?></span>
                                </div>
                            </div>
                            <?php if(isset($this->context["replying_to"]) && $this->context["replying_to"] == $comment["id"]) { ?>
                                <div style="padding-left: 64px; width: 100%; box-sizing: border-box;">
                                    <form class="div-input flex-col" style="gap: 10px; align-items: end; padding-bottom: 10px;" method="post">
                                        <input type="hidden" name="command" value="do-comment">
                                        <input type="hidden" name="resource_id" value="<?php echo $resource_data["id"]; ?>">
                                        <input type="hidden" name="parent_id" value="<?php echo $comment["id"] ?>">
                                        <textarea class="register-input comment-input" rows="4" name="comment" aria-label="Comment entry field" required></textarea>
                                        <button class="styled-button" type="submit">Post comment</button>
                                    </form>
                                </div>
                            <?php } ?>
                            <div class="flex-row">
                                <?php if (!isset($this->context["replying_to"]) || $this->context["replying_to"] != $comment["id"]) { ?>
                                    <form method="get">
                                        <input type="hidden" name="command" value="show-resource">
                                        <input type="hidden" name="target_resource" value="<?php echo $resource_data["id"]; ?>">
                                        <input type="hidden" name="replying_to" value="<?php echo $comment["id"]; ?>">
                                        <button class="link-button" type="submit">Reply</button>
                                    </form>
                                <?php } ?>
                                <?php if ($comment["author"] == $_SESSION["username"] || $resource_data["author"] == $_SESSION["username"]) { ?>
                                    <form method="post">
                                        <input type="hidden" name="command" value="do-delete-comment">
                                        <input type="hidden" name="target_comment" value="<?php echo $comment["id"]; ?>">
                                        <input type="hidden" name="comment_author" value="<?php echo $comment["author"]; ?>">
                                        <input type="hidden" name="target_resource" value="<?php echo $resource_data["id"]; ?>">
                                        <input type="hidden" name="resource_author" value="<?php echo $resource_data["author"]; ?>">
                                        <button class="link-button">Delete comment</button>
                                    </form>
                                <?php } ?>
                            </div>
                            <?php foreach ($comments as $child_comment) { ?>
                                <?php if ($child_comment["parent_id"] == $comment["id"]) { ?>
                                    <div class="comment-container flex-col" style="width: 100%; box-sizing: border-box;">
                                        <div class="comment flex-row">
                                            <img class="comment-pfp" src="styles/pfp.jpg" alt="Commenter profile picture">
                                            <div class="flex-col">
                                                <span style="color: var(--on-secondary-surface);"><?php echo $child_comment["author"]; ?></span>
                                                <span style="color: var(--on-secondary-surface);"><?php echo $child_comment["body"]; ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-row">
                                            <?php if ($child_comment["author"] == $_SESSION["username"] || $resource_data["author"] == $_SESSION["username"]) { ?>
                                                <form method="post">
                                                    <input type="hidden" name="command" value="do-delete-comment">
                                                    <input type="hidden" name="target_comment" value="<?php echo $child_comment["id"]; ?>">
                                                    <input type="hidden" name="comment_author" value="<?php echo $child_comment["author"]; ?>">
                                                    <input type="hidden" name="target_resource" value="<?php echo $resource_data["id"]; ?>">
                                                    <input type="hidden" name="resource_author" value="<?php echo $resource_data["author"]; ?>">
                                                    <button class="link-button">Delete comment</button>
                                                </form>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="spacing"></div>
        </div>
    </body>
</html>