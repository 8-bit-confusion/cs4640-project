<!--
    OpenLearn — Sprint 2 (Static)
    Authors: Natalie Nguyen, Lilli Hrncir, Lily Wasko
    Course: CS 4640 (Fall 2025)
    Deployed URL: https://cs4640.cs.virginia.edu/gzg8pf/project-static/
-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Explore | OpenLearn</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <meta name="author" content="Lily Wasko">
        <!-- site hosted at https://cs4640.cs.virginia.edu/gzg8pf/project-static/ -->
        <link rel="stylesheet" href="./styles/main.css">
        <script src="./explore.js"></script>
    </head>
    <body class="page-fill-body flex-col">
        <header id="main-header" class="flex-row" style="justify-content: space-between; align-items: center;">
            <h1 style="font-weight: normal;">OpenLearn</h1>
            <nav>
                <div id="explore-pfp-anchor" style="position: relative;">
                    <a href="./?command=show-profile" aria-label="Open profile">
                        <img class="profile-picture" src="./styles/pfp.jpg" alt="Profile picture">
                    </a>
                    <div id="explore-pfp-menu" style="visibility: hidden;">
                        <div style="position: relative;"><div id="menu-hitbox"></div></div>
                        <div class="flex-row"><div id="pointer"></div></div>
                        <a href="?command=do-logout" aria-label="Log out of OpenLearn" style="padding: 8px 16px; font-size: 0.9rem;">
                            Log out
                        </a>
                    </div>
                </div>
            </nav>
        </header>
        <div class="flex-col" style="align-items: center; justify-content: start; height: 100%;">
            <div id="search-bar-container" class="flex-col" >
                <form id="search-bar-form" method="get">
                    <div style="position: relative; width: 100%; height: 100%;">
                        <input name="command" value="do-search" hidden>
                        <input name="sort" value="downloads" hidden>
                        <input id="search-bar" type="text" name="q" placeholder="Search for learning resources">
                        <div id="search-button-container" class="flex-col">
                            <button class="flex-col" id="search-button" aria-label="Search" form="search-bar-form" type="submit">
                                <img id="search-button-icon" src="./styles/search-icon.svg" alt="Search icon">
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="browse-sections" class="flex-col" style="align-items: start;">
                <div class="browse-section flex-col">
                    <h2 style="font-weight: normal;">Most downloaded</h2>
                    <div class="browse-row flex-row">
                        <?php
                            foreach ($popular_four as $result) {
                                $result_id = $result["id"];
                                echo "
                                <div>
                                <div class=\"resource-card flex-col\">
                                    <img class=\"resource-preview\" src=\"./styles/img-preview.jpg\" alt=\"resource preview\">
                                    <div class=\"card-title-row flex-row\">
                                        <span class=\"on-resource-card\" style=\"flex-grow: 1;\">{$result["title"]}</span>
                                        <span class=\"on-resource-card resource-downloads\">{$result["download_count"]}</span>
                                        <img class=\"download-icon\" src=\"./styles/download-icon.svg\" alt=\"downloads\">
                                    </div>
                                    <a href=\"?command=show-resource&target_resource=$result_id\" class=\"card-link\" aria-label=\"resource link\"></a>
                                </div>
                                </div>
                                ";
                            }
                        ?>
                    </div>
                </div>
                <div class="browse-section flex-col">
                    <h2 style="font-weight: normal;">Recently uploaded</h2>
                    <div class="browse-row flex-row">
                        <?php
                            foreach ($recent_four as $result) {
                                $result_id = $result["id"];
                                echo "
                                <div>
                                <div class=\"resource-card flex-col\">
                                    <img class=\"resource-preview\" src=\"./styles/img-preview.jpg\" alt=\"resource preview\">
                                    <div class=\"card-title-row flex-row\">
                                        <span class=\"on-resource-card\" style=\"flex-grow: 1;\">{$result["title"]}</span>
                                        <span class=\"on-resource-card resource-downloads\">{$result["download_count"]}</span>
                                        <img class=\"download-icon\" src=\"./styles/download-icon.svg\" alt=\"downloads\">
                                    </div>
                                    <a href=\"?command=show-resource&target_resource=$result_id\" class=\"card-link\" aria-label=\"resource link\"></a>
                                </div>
                                </div>
                                ";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>