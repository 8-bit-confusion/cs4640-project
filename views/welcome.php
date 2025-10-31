<!--
    OpenLearn — Sprint 2 (Static)
    Authors: Natalie Nguyen, Lilli Hrncir, Lily Wasko
    Course: CS 4640 (Fall 2025)
    Deployed URL: https://cs4640.cs.virginia.edu/gzg8pf/project-static/
-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Welcome — OpenLearn</title>
        <meta charset="utf-8">
        <meta name="author" content="Natalie Nguyen">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/main.css">
        <link rel="icon" type="image/png" href="styles/OpenLearnIcon.png">
    </head>

    <body class="page-fill-body flex-col">
        <header id="main-header" class="flex-row" style="justify-content: space-between; align-items: center;">
            <a class="nav-main" href="./welcome.html">
                <h1 style="font-weight: normal">OpenLearn</h1>
            </a>
        </header>

        <!-- main content: two-column (desktop) / stacked (mobile) -->
        <main class="flex-row welcome-layout" style="flex: 1; gap: 32px; padding: 16px;">
            <section class="flex-col" aria-label="Brand">
                <div class="resource-card flex-col" style="align-items: center; justify-content: center; padding: 24px;">
                    <!-- empty alt for screen readers -->
                    <img src="./styles/OpenLearnLogo.png" alt="" style="max-width: 100%; height: auto;">
                </div>
            </section>

            <!-- login -->
            <section class="flex-col" aria-labelledby="welcome-title" style="gap: 16px;">
                <h2 id="welcome-title" style="margin: 0;">Welcome to OpenLearn</h2>
                <p class="on-secondary-surface" style="margin: 0;">
                    Discover and share learning resources with the OpenLearn community.
                </p>

                <form class="flex-col" method="post" style="gap: 16px; margin-top: 8px;">
                    <div class="div-input">
                        <label for="wl-username">Username or email</label><br>
                        <input
                            class="register-input"
                            type="text"
                            id="wl-username"
                            name="username"
                            autocomplete="username"
                            required
                            placeholder="you@example.com">
                    </div>

                    <div class="div-input">
                        <label for="wl-password">Password</label><br>
                        <input
                            class="register-input"
                            type="password"
                            id="wl-password"
                            name="password"
                            autocomplete="current-password"
                            required
                            placeholder="••••••••">
                    </div>

                    <button class="styled-button" type="submit" aria-label="Log in to OpenLearn">
                        Log in
                    </button>

                    <div class="flex-row" style="gap: 16px; align-items: center;">
                        <a class="link-button" href="register.html">Create account</a>
                        <!-- TBD!!! -->
                        <a class="link-button" href="#">Forgot password?</a>
                    </div>
                </form>

                <p class="on-secondary-surface" style="font-size: 0.9rem; margin-top: 8px;">
                    Join OpenLearn to share and discover course materials with fellow educators.
                </p>
            </section>
        </main>
    </body>
</html>
