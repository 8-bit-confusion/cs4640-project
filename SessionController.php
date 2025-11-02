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
            'do-update-profile' => $this->doUpdateProfile(),
            'do-logout' => session_destroy() && $this->showWelcome(),
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

    public function showRegister($message = "", $prefill = []) { // $prefill can have: ['username', 'email', 'display_name']
        $defaults = ['username' => '', 'email' => '', 'display_name' => ''];
        if (!is_array($prefill)) { $prefill = []; }
        $prefill = array_merge($defaults, $prefill);

        include './views/register.php';
    }

    public function showResource($target_resource) {
        $resource_data_result = pg_query_params(
            $this->db_connection,
            "SELECT id, title, author, body, tags, download_count, files
            FROM project_resource 
            WHERE id = ($1)",
            [$target_resource]);
        // $resource_data = pg_fetch_all($resource_data_result)[0];
        // $file_names = [];
        
        // $files = explode(',', substr($resource_data["files"], 1, strlen($resource_data["files"]) - 2));
        // if (count($files) == 1 && $files[0] == '') $files = [];
        $resource_row = pg_fetch_assoc($resource_data_result);
        $resource_data = $resource_row;

        $tags = json_decode($resource_row['tags'], true);
        $files = json_decode($resource_row['files'], true);
        if ($files === null) $files = [];
        $files_names = [];
        $file_data = array();

        foreach ($files as $file_id) {
            $file_name_results = pg_query_params(
                $this->db_connection,
                "SELECT name FROM project_file WHERE id = $1",
                [$file_id]);
            $file_name = pg_fetch_all($file_name_results)[0]["name"];
            array_push($file_data, [$file_name, $file_id]);
        }

        $comments_result = pg_query_params(
            $this->db_connection,
            "SELECT * FROM project_comment WHERE project_comment.resource_id = $1",
            [$target_resource]);
        $comments = pg_fetch_all($comments_result);

        include './views/resource.php';
    }

    public function showProfile($message = "") {
        $res = pg_query_params(
            $this->db_connection,
            "SELECT username, display_name
            FROM project_user
            WHERE username = $1",
            [$_SESSION['username']]
        );
        $user = pg_fetch_assoc($res) ?: [
            'username'     => $_SESSION['username'],
            'display_name' => $_SESSION['display_name'] ?? ''
        ];

        $resources_result = pg_query_params(
            $this->db_connection,
            "SELECT id, title, download_count FROM project_resource WHERE author = $1 ORDER BY id DESC",
            [$_SESSION['username']]
        );
        $resources = pg_fetch_all($resources_result) ?: [];

        $flashMessage = $message;
        include './views/profile.php';
    }

    public function showCreate() {
        include './views/create.php';
    }

    public function showExplore() {
        $popular_four_result = pg_query_params(
            $this->db_connection,
            "SELECT * FROM project_resource ORDER BY project_resource.download_count DESC LIMIT 4", []);
        $popular_four = pg_fetch_all($popular_four_result);

        $recent_four_result = pg_query_params(
            $this->db_connection,
            "SELECT * FROM project_resource ORDER BY project_resource.id DESC LIMIT 4", []);
        $recent_four = pg_fetch_all($recent_four_result);

        include './views/explore.php';
    }

    public function showSearch() {
        include './views/search.php';
    }

    // PROCESS COMMAND FUNCTIONS ###################################################################################

    public function doLogin() {
        $username = $this->context["username"];
        $password = $this->context["password"];

        // check if password field is empty
        if ($password === '') {
            $this->showWelcome("Password is required.");
            return;
        }

        // determine whether the input is a username or an email
        $isEmail = (strpos($username, '@') !== false);

        if ($isEmail) {
            // if the input contains '@', treat it as an email
            if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $username)) {
                $this->showWelcome("Please enter a valid email like name@example.com.");
                return;
            }

            // check the database to see if this email exists
            $user_in_db_result = pg_query_params(
                $this->db_connection,
                "SELECT username FROM project_user WHERE project_user.email = $1",
                [$username]);
            $user_in_db = pg_fetch_all($user_in_db_result);

            // if the email does not exist, display an error and
            // navigate back to the welcome page
            if (count($user_in_db) == 0) {
                $this->showWelcome("Email not found. Did you mean to register an account?");
                return;
            }

            // extract the corresponding username for session storage
            $username = $user_in_db[0]["username"];
        }

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
        $email = $this->context["email"];
        $display_name = $this->context["display_name"];
        $password = (string)($this->context["pwd"]);
        $confirm_password = (string)($this->context["retype_pwd"]);

        $prefill = [
            'username' => $username,
            'email' => $email,
            'display_name' => $display_name
        ];

        // validate inputs
        if (!preg_match('/^[A-Za-z0-9_-]{3,12}$/', $username)) {
            $this->showRegister("Username must be 3–12 characters using letters, digits, _ or -.", $prefill);
            return;
        }
        if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            $this->showRegister("Please enter a valid email like name@example.com.", $prefill);
            return;
        }
        if ($password === '' || $confirm_password === '') {
            $this->showRegister("Password and confirmation are required.", $prefill);
            return;
        }
        if ($password !== $confirm_password) {
            $this->showRegister("Passwords do not match.", $prefill);
            return;
        }

        // check the database to see if the username or email is taken
        $existsUser = pg_query_params(
            $this->db_connection,
            "SELECT 1 FROM project_user WHERE username = $1",
            [$username]
        );
        if (pg_fetch_assoc($existsUser)) {
            $this->showRegister("Username is already in use. Did you mean to log in?", $prefill);
            return;
        }

        $existsEmail = pg_query_params(
            $this->db_connection,
            "SELECT 1 FROM project_user WHERE email = $1",
            [$email]
        );
        if (pg_fetch_assoc($existsEmail)) {
            $this->showRegister("Email is already in use. Try logging in instead.", $prefill);
            return;
        }

        // if registration is successful, hash the user's password
        // and add their username, display name, email, and hashed password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $ok = pg_query_params(
            $this->db_connection,
            "INSERT INTO project_user (username, email, display_name, password_hash)
            VALUES ($1, $2, $3, $4)",
            [$username, $email, $display_name, $password_hash]
        );

        if ($ok === false) {
            $this->showRegister("Failed to create account. Please try again.", $prefill);
            return;
        }

        $_SESSION["username"] = $username;
        $_SESSION["display_name"] = $display_name;

        $this->showExplore();
    }

    public function doCreate() {
        $title = $this->context["title"];
        $description = $this->context["description"];
        $serial_ids = [];
        $tags = explode(" ", $this->context["tags"]);

        $file_names = $_FILES['files']['name'];
        $tmp_names = $_FILES['files']['tmp_name'];

        for ($i = 0; $i < count($file_names); $i++) {
            $file_name = $file_names[$i];
            $tmp_name = $tmp_names[$i];
            $upload_dir = 'uploads/';
            $destination = $upload_dir . basename($file_name);   

            if (!move_uploaded_file($tmp_name, $destination)) {
                echo "Error uploading '{$file_name}'.<br>";
            }

            $file_path = __DIR__ . '/uploads/' . $file_name;
            $upload_key = $upload_dir . basename($file_path);

            $file_keys = $this->bucket->upload($upload_key, $file_path);
            $serial_id_result = pg_query_params(
                $this->db_connection,
                "INSERT INTO project_file (aws_key, url, name) VALUES ($1, $2, $3) RETURNING id",
                [$file_keys[0], $file_keys[1], $file_name]);
            $serial_id = pg_fetch_all($serial_id_result)[0]["id"];
            
            array_push($serial_ids, $serial_id);
        }

        $target_resource_result = pg_query_params(
            $this->db_connection,
            "INSERT INTO project_resource (author, title, body, tags, download_count, files) VALUES ($1, $2, $3, $4, 0, $5) RETURNING id",
            [$_SESSION["username"], $title, $description, json_encode($tags), json_encode($serial_ids)]);
        if (!$target_resource_result) {
            echo "Failed to insert new resource";
            return;
        }
        $target_resource = pg_fetch_all($target_resource_result)[0]["id"];
                
        $this->showResource($target_resource);
    }


    public function doSearch() {
        $query = $this->context["q"];

        list($sort_key, $sort_order) = match ($this->context["sort"]) {
            "downloads" => [" download_count", " DESC"],
            "newest" => [" id", " DESC"],
            "oldest" => [" id", ""],
        };

        $sql_query = "SELECT * FROM project_find_resource_by_tag($1) ORDER BY" . $sort_key . $sort_order;

        $search_results_result = pg_query_params(
            $this->db_connection,
            $sql_query,
            [$query]);
        $search_results = pg_fetch_all($search_results_result);
        include './views/search.php';
    }

    public function doDelete() {
        if ($_SESSION["username"] != $this->context["resource_author"]) {
            return; // don't delete anything if we aren't the owner.
        }

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
        $file_key = $this->context["file-key"];
        $this->bucket->download($file_key);
    }

    public function doUpdateProfile() {
        $sessionUser = $_SESSION["username"];
        $formUser = trim($this->context["username"] ?? '');
        $newDisplay = trim($this->context["display_name"] ?? '');

        // update username if it does not already exist
        if (!isset($_SESSION["username"])) {
            $this->showProfile("This username already exists. Try a different name.");
        }

        if ($formUser !== $sessionUser) {
            $this->showProfile("You can only update your own profile.");
            return;
        }

        if ($newDisplay === '') {
            $this->showProfile("Display name is required.");
            return;
        }

        if (mb_strlen($newDisplay) > 100) {
            $this->showProfile("Display name is too long (maximum 100 characters).");
            return;
        }

        $ok = pg_query_params(
            $this->db_connection,
            "UPDATE project_user SET display_name = $1 WHERE username = $2",
            [$newDisplay, $sessionUser]
        );
        
        if ($ok === false) {
            $this->showProfile("Failed to save changes.");
            return;
        }

        $_SESSION["display_name"] = $newDisplay;

        $this->showProfile("Profile updated successfully.");

    }

    public function doComment() {
        $ok;

        if ($this->context["parent_id"] == "null") {
            $ok = pg_query_params(
                $this->db_connection,
                "INSERT INTO project_comment (resource_id, author, parent_id, body) VALUES ($1, $2, NULL, $3)",
                [$this->context["resource_id"], $_SESSION["username"], $this->context["comment"]]);
        } else {
            $ok = pg_query_params(
                $this->db_connection,
                "INSERT INTO project_comment (resource_id, author, parent_id, body) VALUES ($1, $2, $3, $4)",
                [$this->context["resource_id"], $_SESSION["username"], $this->context["parent_id"], $this->context["comment"]]);
        }
        
        $this->showResource($this->context["resource_id"]);
    }
}

/*

for (file in from_result) {
    pg_query(insert file into files table) ----> puts file record at some serial id
}

pg_query(insert resource with files $1, [[file_id_1, file_id_2]])

*/

?>