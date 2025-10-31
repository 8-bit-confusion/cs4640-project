<?php

class SessionController {
    private $db_connection;
    private $context;
    private $bucket;

    public function __construct() {
        session_start();
        // change to ::$server_db when deploying
        $config = new Config("./.env");

        // AWS S3 Bucket setup
        $this->bucket = new Bucket($config);
        
        $db_config = $config->local_db();

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
                'show-welcome', 'show-register', 'do-login', 'do-register' => $command,
                default => 'show-welcome',
            };
        }
        
        match ($command) {
            // nav commands
            'show-welcome' => $this->showWelcome(),
            'show-register' => $this->showRegister(),

            'show-resource' => $this->showResource($this->context["target_resource"]),
            'show-profile' => $this->showProfile(),
            'show-create' => $this->showCreate(),
            'show-explore' => $this->showExplore(),
            'show-search' => $this->showSearch(),
            
            // process commands
            'do-login' => $this->doLogin(),
            'do-register' => $this->doRegister(),
            'do-search' => $this->doSearch(),
            'do-create' => $this->doCreate(),
            'do-comment' => $this->doComment(),
            'do-delete' => $this->doDelete(),
        };
    }

    // NAV COMMAND FUNCTIONS #######################################################################################

    // the $message parameter in the functions can be accessed from the
    // included .php views---this allows them to return the error message
    // at a chosen point on the page, rather than just echoing it at the
    // top or bottom of the <body> tag.
    public function showWelcome($message = "") {
        include './views/welcome.php';
    }

    public function showRegister($message = "") {
        include './views/register.php';
    }

    public function showResource($target_resource) {
        $resource_data_result = pg_query_params(
            $this->db_connection,
            "SELECT id, title, author, body, tags, download_count, array_to_json(files) 
            AS files_json 
            FROM project_resource 
            WHERE id = ($1)",
            [$target_resource]);
        // $resource_data = pg_fetch_all($resource_data_result)[0];
        // $file_names = [];
        
        // $files = explode(',', substr($resource_data["files"], 1, strlen($resource_data["files"]) - 2));
        // if (count($files) == 1 && $files[0] == '') $files = [];
        $resource_row = pg_fetch_assoc($resource_data_result);
        $resource_data = $resource_row;

        $files = json_decode($resource_row['files_json'], true);
        if ($files === null) $files = [];
        $files_names = [];

        foreach ($files as $file_id) {
            $file_name_results = pg_query_params(
                $this->db_connection,
                "SELECT name FROM project_file WHERE id = $1",
                [$file_id]);
            $file_name = pg_fetch_all($file_name_results)[0]["name"];
            $file_names[] = $file_name;
        }
        include './views/resource.php';
    }

    public function showProfile() {
        include './views/profile.php';
    }

    public function showCreate() {
        include './views/create.php';
    }

    public function showExplore() {
        include './views/explore.php';
    }

    public function showSearch() {
        include './views/search.php';
    }

    // PROCESS COMMAND FUNCTIONS ###################################################################################

    public function doLogin() {
        $username = $this->context["username"];
        $password = $this->context["password"];

        // check the database to see if this username exists
        $user_in_db_result = pg_query_params(
            $this->db_connection,
            "SELECT username FROM project_user WHERE project_user.username = $1",
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
            "SELECT password_hash FROM project_user WHERE username = $1",
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
            "SELECT display_name FROM project_user WHERE username = $1",
            [$username]);
        $display_name = pg_fetch_all($display_name_result)[0]["display_name"];

        $_SESSION["username"] = $username;
        $_SESSION["display_name"] = $display_name;

        // default to the explore page after logging in
        $this->showExplore();
    }

    public function doRegister() {
        $username = $this->context["username"];
        $display_name = $this->context["display_name"];
        $password = $this->context["pwd"];
        $confirm_password = $this->context["retype_pwd"];

        // if the inputted passwords don't match each other, display
        // an error and navigate back to the register page
        if ($password != $confirm_password) {
            $this->showRegister("Passwords do not match.");
            return;
        }

        // check the database to see if the username is taken
        $user_in_db_result = pg_query_params(
            $this->db_connection,
            "SELECT username FROM project_user WHERE project_user.username = $1",
            [$username]);
        $user_in_db = pg_fetch_all($user_in_db_result);

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
            "INSERT INTO project_user (username, display_name, password_hash) VALUES ($1, $2, $3)",
            [$username, $display_name, $password_hash]);

        $_SESSION["username"] = $username;
        $_SESSION["display_name"] = $display_name;

        // default to the explore page after registering
        $this->showExplore();
    }

    public function doCreate() {
        $title = $this->context["title"];
        $description = $this->context["description"];
        $files = $_FILES['files'];
        $serial_ids = [];
        // Handle files recieved from POST form
        // foreach ($this->context["files"] as $file){
        //     array_push($files, $file);
        // }
        $tags = explode(" ", $this->context["tags"]);

        foreach ($files as $object) {
            $file_keys = $this->bucket->upload($object["name"]);
            // Name is placeholder
            $serial_id_result = pg_query_params(
                $this->db_connection,
                "INSERT INTO project_file () VALUES ($1, $2, $3) RETURNING ID",
                [$file_keys[0], $file_keys[1], $object['name']]);
            $serial_id = pg_fetch_all($serial_id_result)[0]["id"];
            
            array_push($serial_ids, $serial_id);
        }
        $target_resource_result = pg_query_params(
            $this->db_connection,
            "INSERT INTO project_resource (author, title, body, tags, download_count, files) VALUES ($1, $2, $3, $4, 0, $5) RETURNING ID",
            [$_SESSION["username"], $title, $description, $tags, $serial_ids]);
        $target_resource = pg_fetch_all($target_resource_result)[0]["id"];
                
        $this->showResource($target_resource);
    }

    public function doSearch() {
        $query = $this->context["q"];

        $search_results_result = pg_query_params(
            $this->db_connection,
            "SELECT * FROM project_find_resource_by_tag($1)",
            [$query]);
        $search_results = pg_fetch_all($search_results_result);
        include './views/search.php';
    }

    public function doDelete() {
        $resource = $this->context['target_resource'];
        $file_ids_result = pg_query_params(
            $this->db_connection,
            "SELECT files FROM project_resource WHERE id = ($1)",
            [$resource]);
        // $file_ids = pg_fetch_all($file_ids_result)[0]["files"];

        // $file_ids = explode(',', substr($file_ids, 1, strlen($file_ids) - 2));
        // if (count($file_ids) == 1 && $file_ids[0] == '') $file_ids = [];
        $row = pg_fetch_assoc($file_ids_result);
        $file_ids=json_decode($row['file_ids'], true);
        if ($file_ids === null) $file_ids = [];

        foreach ($file_ids as $file_id) {
            $file_key = pg_query_params(
                $this->db_connection,
                "SELECT s3_key FROM project_file WHERE id = ($1)",
                [$file_id]);
            this->bucket->delete($file_key);
        }
        pg_query_params(
            $this->db_connection,
            "DELETE FROM project_resource WHERE id = ($1)",
            [$resource]);
        
        $this->showExplore();
    }

    public function doDownload() {

    }
}

/*

for (file in from_result) {
    pg_query(insert file into files table) ----> puts file record at some serial id
}

pg_query(insert resource with files $1, [[file_id_1, file_id_2]])

*/

?>