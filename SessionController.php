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
        $command = 'show-welcome';
        if (isset($this->context['command']))
            $command = $this->context['command'];

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
        };
    }

    // PROCESS COMMAND FUNCTIONS ###################################################################################

    public function doLogin() {
        $username = $this->context["username"];
        $password = $this->context["password"];

        $user_in_db_result = pg_query_params(
            $this->db_connection,
            "SELECT username FROM project_users WHERE project_users.username = $1",
            [$username]);
        $user_in_db = pg_fetch_all($user_in_db_result);

        if (count($user_in_db) == 0) {
            $this->showWelcome("Username not found. Did you mean to register an account?");
            return;
        }

        $db_hash_result = pg_query_params(
            $this->db_connection,
            "SELECT password_hash FROM project_users WHERE username = $1",
            [$username]);
        $db_hash = pg_fetch_all($db_hash_result)[0]["password_hash"];

        if (!password_verify($password, $db_hash)) {
            $this->showWelcome("Password was incorrect. Please try again.");
            return;
        }

        $_SESSION["username"] = $username;
        $display_name_result = pg_query_params(
            $this->db_connection,
            "SELECT display_name FROM project_users WHERE username = $1",
            [$username]);
        $display_name = pg_fetch_all($display_name_result)[0]["display_name"];
        $_SESSION["display_name"] = $display_name;

        $this->showExplore();
    }

    public function doRegister() {
        $username = $this->context["username"];
        $display_name = $this->context["displayName"];
        $password = $this->context["password"];
        $confirm_password = $this->context["confirmPassword"];

        if ($password != $confirm_password) {
            $this->showRegister("Passwords do not match.");
            return;
        }

        $user_in_db_result = pg_query_params(
            $this->db_connection,
            "SELECT username FROM project_users WHERE project_users.username = $1",
            [$username]);
        $user_in_db = pg_fetch_all($email_in_db_result);

        if (count($user_in_db) > 0) {
            $this->showRegister("Username is already in use. Did you mean to login?");
            return;
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        pg_query_params(
            $this->db_connection,
            "INSERT INTO project_users (username, display_name, password_hash) VALUES ($1, $2, $3)",
            [$username, $display_name, $password_hash]);

        $_SESSION["username"] = $username;
        $_SESSION["display_name"] = $displayName;
        $this->showExplore();
    }

    public function loginOLD() {
        $username = $this->context["username"];
        $password = $this->context["password"];
        
        $email_in_db_result = pg_query_params($this->dbConnection, "SELECT email FROM hw3_users WHERE hw3_users.email = $1", [$email]);
        $email_in_db = pg_fetch_all($email_in_db_result);

        if (count($email_in_db) > 0) {
            $db_hash_result = pg_query_params($this->dbConnection, "SELECT password_hash FROM hw3_users WHERE email = $1", [$email]); // get password where email is the current email
            $db_hash = pg_fetch_all($db_hash_result)[0]["password_hash"];

            if (!password_verify($password, $db_hash)) {
                $this->welcome(true, "Password was incorrect. Please try again.");
                return;
            }
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            pg_query_params($this->dbConnection, "INSERT INTO hw3_users (name, email, password_hash) VALUES ($1, $2, $3)", [$username, $email, $password_hash]); // insert username, email, & password into table
        }

        $_SESSION["username"] = $username;
        $_SESSION["email"] = $email;

        // get the current user's user_id so we can link IT into the games table
        $user_id_result = pg_query_params($this->dbConnection, "SELECT user_id FROM hw3_users WHERE hw3_users.email = $1", [$_SESSION["email"]]);
        $user_id = pg_fetch_all($user_id_result)[0]["user_id"];
        $_SESSION["user_id"] = $user_id;

        $this->startGame();
    }
}

?>