<!--
    OpenLearn — Sprint 2 (Static)
    Authors: Natalie Nguyen, Lilli Hrncir, Lily Wasko
    Course: CS 4640 (Fall 2025)
    Deployed URL: https://cs4640.cs.virginia.edu/gzg8pf/project-static/
-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Search | OpenLearn</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <meta name="author" content="Lily Wasko">
        <!-- site hosted at https://cs4640.cs.virginia.edu/gzg8pf/project-static/ -->
        <link rel="stylesheet" href="styles/main.css">
    </head>
    <body class="page-fill-body flex-col">
        <header id="main-header" class="flex-row" style="justify-content: space-between; align-items: center;">
            <a class="nav-main" href="./?command=show-explore"><h1 style="font-weight: normal;">OpenLearn</h1></a>
            <nav>
                <a href="./?command=show-profile" aria-label="Open profile">
                    <img class="profile-picture" src="styles/pfp.jpg" alt="Profile picture">
                </a>
            </nav>
        </header>
        <div class="flex-col" style="align-items: center; justify-content: start;">
            <div id="search-bar-container" class="flex-col" >
                <form id="search-bar-form" class="flex-col" style="align-items: center; gap: 8px;" method="get">
                    <div style="position: relative; width: 100%; height: 50px;">
                        <input name="command" value="do-search" hidden>
                        <?php
                            echo "<input id=\"search-bar\" type=\"text\" name=\"q\" value=\"$query\" placeholder=\"Search for learning resources by title or author\">";
                        ?>
                        <div id="search-button-container" class="flex-col">
                            <button class="flex-col" id="search-button" aria-label="Search" form="search-bar-form" type="submit">
                                <img id="search-button-icon" src="styles/search-icon.svg" alt="Search icon">
                            </button>
                        </div>
                    </div>
                    <div class="flex-row">
                        <label for="sort">Sort results by:</label>
                        <select name="sort" id="sort">
                            <option value="downloads"># of Downloads</option>
                            <option value="newest">Upload date (newest)</option>
                            <option value="oldest">Upload date (oldest)</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div id="search-results" class="flex-row" style="flex-grow: 1; flex-wrap: wrap; overflow-y: auto; align-self: center; width: 720px;">
            <?php

                foreach ($search_results as $result) {
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
    </body>
</html>