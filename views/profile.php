<!--
    OpenLearn — Sprint 2 (Static)
    Authors: Natalie Nguyen, Lilli Hrncir, Lily Wasko
    Course: CS 4640 (Fall 2025)
    Deployed URL: https://cs4640.cs.virginia.edu/gzg8pf/project-static/
-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Profile — OpenLearn</title>
        <meta charset="utf-8">
        <meta name="author" content="OpenLearn">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/main.css">
        <link rel="icon" type="image/png" href="styles/OpenLearnIcon.png">
    </head>

    <body class="page-fill-body flex-col">
        <header id="main-header" class="flex-row" style="justify-content: space-between; align-items: center;">
            <a class="nav-main" href="?command=show-explore">
                <h1 style="font-weight: normal">OpenLearn</h1>
            </a>
            <nav class="flex-row" style="align-items: center; gap: 16px;">
                <a href="?command=show-profile" aria-label="Open profile">
                    <!-- STATIC!! GOTTA CHANGE (once we get imgs set up) -->
                    <img class="profile-picture" src="styles/pfp.jpg" alt="Profile picture">
                </a>
                <span class="on-resource-card" style="opacity:.8;">
                    <?= htmlspecialchars($_SESSION['display_name'] ?? $user['display_name'] ?? '') ?>
                </span>
                <a class="styled-button" href="?command=do-logout" aria-label="Log out of OpenLearn" style="padding: 8px 16px; font-size: 0.9rem;">
                    Log out
                </a>
            </nav>

        </header>

        <main class="flex-row profile-layout" style="flex: 1; gap: 24px; padding: 16px;">
            <!-- pfp info section -->
            <section class="flex-col" aria-labelledby="profile-heading" style="flex: 1; gap: 16px;">
                <div class="resource-card flex-col" style="gap: 16px; padding: 24px;">
                    <h2 id="profile-heading" style="margin: 0;">Your Profile</h2>

                    <img src="styles/pfp.jpg" alt="User profile picture placeholder"
                        style="width: 96px; height: 96px; border-radius: 9999px; object-fit: cover;">

                    <?php if (!empty($flashMessage)): ?>
                    <div class="flash" style="color:#155724;background:#d4edda;border:1px solid #c3e6cb;padding:8px;border-radius:6px;">
                        <?= htmlspecialchars($flashMessage) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php $bioMax = 200; ?>
                    <form class="flex-col" method="post" action="index.php" style="gap: 16px;">
                        <input type="hidden" name="command" value="do-update-profile">
                        <div class="div-input">
                            <label for="pf-display">Display name</label><br>
                            <input class="register-input" type="text" id="pf-display" name="display_name" required maxlength="100" value="<?= htmlspecialchars($user['display_name']) ?>" placeholder="Your display name">
                        </div>

                        <div class="div-input">
                            <label for="pf-username">Username</label><br>
                            <input class="register-input"
                                type="text"
                                id="pf-username"
                                value="<?= '@' . htmlspecialchars($user['username']) ?>"
                            readonly>
                        </div>

                        <div class="div-input">
                        <label for="pf-bio">Description</label><br>
                        <textarea class="register-input"
                                    id="pf-bio"
                                    name="bio"
                                    rows="4"
                                    maxlength="<?= htmlspecialchars($bioMax) ?>"
                                    placeholder="Tell others about your interests…"><?=
                            htmlspecialchars($user['bio'])
                        ?></textarea>
                        </div>
                        <button class="styled-button" type="submit" aria-label="Save profile changes">Save</button>
                    </form>
                </div>
            </section>

            <!-- contributed resources section -->
            <section class="flex-col" aria-labelledby="contrib-heading" style="flex: 2; gap: 16px;">
                <a class="styled-button" href="?command=show-create" style="align-self: center; text-align: center; display: inline-block;">
                    Create New Resource
                </a>
                <h2 id="contrib-heading" style="margin-top: 8px;">Contributed Resources</h2>

                <?php if (!empty($resources)): ?>
                    <?php foreach ($resources as $resource): ?>
                        <article class="resource-card flex-col" style="position: relative;">
                            <img class="resource-preview" src="styles/img-preview.jpg" alt="Resource preview">
                            <div class="card-title-row flex-row">
                                <h3 class="on-resource-card" style="flex-grow: 1;">
                                    <?= htmlspecialchars($resource['title']) ?>
                                </h3>
                                <span class="on-resource-card resource-downloads">
                                    <?= htmlspecialchars($resource['download_count']) ?>
                                </span>
                                <img class="download-icon" src="styles/download-icon.svg" alt="downloads">
                            </div>
                            <a href="?command=show-resource&target_resource=<?= urlencode($resource['id']) ?>"
                            class="card-link"
                            aria-label="Open resource: <?= htmlspecialchars($resource['title']) ?>"></a>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="opacity:.7;">You haven’t uploaded any resources yet.</p>
                <?php endif; ?>

            </section>
        </main>
    </body>
</html>