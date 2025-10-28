<?php

class AnagramsGameController {
    private $db_connection;
    private $context;

    public function __construct() {
        session_start();

        // change to ::$server_db when deploying
        $db_config = DBConfig::$local_db;

        $host = $db_config["host"];
        $user = $db_config["user"];
        $database = $db_config["database"];
        $password = $db_config["pass"];
        $port = $db_config["port"];

        $this->db_connection = pg_connect("host=$host port=$port dbname=$database user=$user password=$password");

        // get input from appropriate request context
        $this->context = match($_SERVER['REQUEST_METHOD']) {
            'GET' => $_GET,
            'POST' => $_POST,
        };
    }

    public function run() {
        // if there is no command in the url, we should navigate to the welcome page
        $command = 'show-welcome';
        if (isset($this->context['command']))
            $command = $this->context['command'];

        // if the user isn't signed in yet, we should only allow commands for viewing
        // the welcome and register pages and for the logging in and registering actions.
        // if the command is something else, change it to showing the welcome page.
        if (!isset($_SESSION["username"])) {
            $command = match($command) {
                'show-welcome', 'show-login', 'do-login', 'do-register' => $command,
                default => 'show-welcome',
            };
        }
        
        match ($command) {
            // nav commands
            'show-welcome' => $this->showWelcome(),
            'show-register' => $this->showRegister(),

            'show-resource' => $this->showResource($this->context["target_resource"]),
            'show-profile' => $this->showProfile($_SESSION["username"]),
            'show-create' => $this->showCreate(),
            'show-explore' => $this->showExplore(),
            
            // process commands
            'do-login' => $this->doLogin(),
            'do-register' => $this->doRegsiter(),
            'do-search' => $this->doSearch(),
        };
    }

    // NAV COMMAND FUNCTIONS #######################################################################################

    // the $message parameter in the functions can be accessed from the
    // included .php views---this allows them to return the error message
    // at a chosen point on the page, rather than just echoing it at the
    // top or bottom of the <body> tag.
    public function showWelcome($message = "") {
        include './views/welcome.html'; // TODO: change to .php
    }

    public function showRegister($message = "") {
        include './views/register.html'; // TODO: change to .php
    }

    // PROCESS COMMAND FUNCTIONS ###################################################################################

    public function doLogin() {
        $username = $this->context["username"];
        $password = $this->context["password"];

        // check the database to see if this username exists
        $user_in_db_result = pg_query_params(
            $this->db_connection,
            "SELECT username FROM project_users WHERE project_users.username = $1",
            [$username]);
        $user_in_db = pg_fetch_all($user_in_db_result);

        // if the username does not exist, display an error and
        // navigate back to the welcome page
        if (count($user_in_db) == 0) {
            $this->showWelcome("Username not found. Did you mean to register an account?");
            return;
        }

        // check the database for the hash of this user's password
        $db_hash_result = pg_query_params(
            $this->db_connection,
            "SELECT password_hash FROM project_users WHERE username = $1",
            [$username]);
        $db_hash = pg_fetch_all($db_hash_result)[0]["password_hash"];

        // if the inputted password does not match the hash, display
        // an error and navigate back to the welcome page
        if (!password_verify($password, $db_hash)) {
            $this->showWelcome("Password was incorrect. Please try again.");
            return;
        }

        // if login is successful, query the display name of the user
        // so it can be added to the $_SESSION variable and accessed
        // in the dynamic views
        $display_name_result = pg_query_params(
            $this->db_connection,
            "SELECT display_name FROM project_users WHERE username = $1",
            [$username]);
        $display_name = pg_fetch_all($display_name_result)[0]["display_name"];

        $_SESSION["username"] = $username;
        $_SESSION["display_name"] = $display_name;

        // default to the explore page after logging in
        $this->showExplore();
    }

    public function doRegister() {
        $username = $this->context["username"];
        $display_name = $this->context["displayName"];
        $password = $this->context["password"];
        $confirm_password = $this->context["confirmPassword"];

        // if the inputted passwords don't match each other, display
        // an error and navigate back to the register page
        if ($password != $confirm_password) {
            $this->showRegister("Passwords do not match.");
            return;
        }

        // check the database to see if the username is taken
        $user_in_db_result = pg_query_params(
            $this->db_connection,
            "SELECT username FROM project_users WHERE project_users.username = $1",
            [$username]);
        $user_in_db = pg_fetch_all($email_in_db_result);

        // if the username is already in use, display an error and
        // navigate back to the register page
        if (count($user_in_db) > 0) {
            $this->showRegister("Username is already in use. Did you mean to login?");
            return;
        }

        // if registration is successful, hash the user's password
        // and add their username, display name, and hashed password
        // to the database
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        pg_query_params(
            $this->db_connection,
            "INSERT INTO project_users (username, display_name, password_hash) VALUES ($1, $2, $3)",
            [$username, $display_name, $password_hash]);

        $_SESSION["username"] = $username;
        $_SESSION["display_name"] = $displayName;

        // default to the explore page after registering
        $this->showExplore();
    }
}

?>