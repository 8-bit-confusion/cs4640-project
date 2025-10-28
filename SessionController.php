<?php

class AnagramsGameController {
    private $dbConnection;
    private $context;

    private $shortWords;
    private $sevenWords;

    public function __construct() {
        session_start();

        $host = DBConfig::$db["host"];
        $user = DBConfig::$db["user"];
        $database = DBConfig::$db["database"];
        $password = DBConfig::$db["pass"];
        $port = DBConfig::$db["port"];

        $this->dbConnection = pg_connect("host=$host port=$port dbname=$database user=$user password=$password");

        // get input from appropriate request context
        $this->context = match($_SERVER['REQUEST_METHOD']) {
            'GET' => $_GET,
            'POST' => $_POST,
        };

        $shortWordsFile = file_get_contents("/var/www/html/homework/word_bank.json");
        $this->shortWords = json_decode($shortWordsFile, true);
        
        $sevenWordsFile = file_get_contents("/var/www/html/homework/words7.txt");
        $this->sevenWords = preg_split("/\R/", $sevenWordsFile);
    }

    public function run() {
        $command = 'welcome';
        if (isset($this->context['command']))
            $command = $this->context['command'];

        if (!isset($_SESSION["username"]) && $command != 'login') {
            $this->welcome();
            return;
        }
        
        match ($command) {
            'welcome' => $this->welcome(),
            'login' => $this->login(),
            'start-game' => $this->startGame(),
            'guess' => $this->processGuess(),
            'game-over' => $this->gameover(),
            'logout' => $this->logout(),
            'shuffle' => $this->reshuffle(),
            'quit' => $this->gameover(true),
        };
    }

    // COMMAND FUNCTIONS ###########################################################################################

    public function welcome($showMessage = false, $message = "") {
        include "./views/welcome.php";
    }

    public function login() {
        $username = $this->context["username"];
        $email = $this->context["email"];
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

    public function startGame() {
        $_SESSION["guessedWords"] = array();
        $_SESSION["invalidGuesses"] = 0;
        $_SESSION["score"] = 0;
        $this->chooseShuffledString();
        $response = "";
        include "./views/game.php";
    }

    public function processGuess() {
        $guess = $this->context["guess"];
        $response = "";
        if (in_array(strtolower($guess), $_SESSION["guessedWords"])) {
            $response = "You already guessed this!";
            include "./views/game.php";
            $_SESSION["invalidGuesses"] += 1;
        }
        elseif (!$this->validLetters($guess, $_SESSION["shuffledString"])) {
            $response = "Guess has invalid letters.";
            include "./views/game.php";
            $_SESSION["invalidGuesses"] += 1;
        }
        elseif (!$this->validWord($guess)) {
            $response = "Guess is not a word.";
            include "./views/game.php";
            $_SESSION["invalidGuesses"] += 1;
        }
        elseif (strlen($guess) < 7) {
            $points = match(strlen($guess)) {
                1 => 1,
                2 => 2,
                3 => 4,
                4 => 8,
                5 => 15,
                6 => 30,
            };

            $_SESSION["score"] += $points;
            array_push($_SESSION["guessedWords"], strtolower($guess));
            $response = "Congratulations on finding a valid word! +$points points";
            include "./views/game.php";
        }
        else {
            $this->gameover(false);
        }
    }

    public function reshuffle() {
        $response = "";
        $_SESSION["shuffledString"] = str_shuffle($_SESSION["shuffledString"]);
        include "./views/game.php";
    }

    public function logout() {
        session_destroy();

        session_start();
        $this->welcome();
    }

    public function gameover($quit = false) {
        // this word has been played, so add it to the hw3_words table
        $word_in_db_result = pg_query_params($this->dbConnection, "SELECT word FROM hw3_words WHERE hw3_words.word = $1", [$_SESSION["targetWord"]]);
        $word_in_db = pg_fetch_all($word_in_db_result);
        
        if (count($word_in_db) == 0) {
            pg_query_params($this->dbConnection, "INSERT INTO hw3_words (word) VALUES ($1)", [$_SESSION["targetWord"]]);
        }

        // get the associated word id so we can link it into the games table
        $word_id_result = pg_query_params($this->dbConnection, "SELECT word_id FROM hw3_words WHERE hw3_words.word = $1", [$_SESSION["targetWord"]]);
        $word_id = pg_fetch_all($word_id_result)[0]["word_id"];

        // add the game data to the games table
        $did_quit = match($quit) {
            true => '1',
            false => '0',
        };

        $did_win = match(!$quit) {
            true => '1',
            false => '0',
        };

        pg_query_params($this->dbConnection, "INSERT INTO hw3_games (user_id, word_id, score, won, quit_early) VALUES ($1, $2, $3, $4, $5)", [$_SESSION["user_id"], $word_id, $_SESSION["score"], $did_win, $did_quit]);
        include "./views/game-over.php";
    }

    // PRIVATE FUNCTIONS ###########################################################################################

    // Turns the string into a 'set' of chars to see
    private function validLetters($guess, $word) {
        $guess_chars = str_split(strtolower($guess));
        $word_chars = str_split(strtolower($word));

        sort($guess_chars);
        sort( $word_chars );

        foreach ($guess_chars as $char) {
            if (!in_array($char, $word_chars)) { return false; }
            array_shift($word_chars);
        }

        return true;
    }

    private function validWord($guess) {
        if (strlen($guess) < 6) {
            $length_array = $this->shortWords[(string) strlen($guess)];
            return in_array(strtolower($guess), $length_array);
        }
        
        return in_array(strtolower($guess), $this->sevenWords);
    }


    // note from lily to lilli---removed return
    // value here since refactoring means we can just
    // set a class variable
    private function chooseShuffledString() {
        $past_words_result = pg_query_params($this->dbConnection, "SELECT word FROM hw3_games INNER JOIN hw3_words ON hw3_words.word_id = hw3_games.word_id WHERE hw3_games.user_id = $1", [$_SESSION["user_id"]]);
        $past_words = array_map(function($a) { return $a["word"]; }, pg_fetch_all($past_words_result));

        $_SESSION["targetWord"] = $this->sevenWords[array_rand($this->sevenWords)];
        while (in_array($_SESSION["targetWord"], $past_words)) {
            $_SESSION["targetWord"] = $this->sevenWords[array_rand($this->sevenWords)];
        }

        $_SESSION["shuffledString"] = str_shuffle($_SESSION["targetWord"]);
    }
}

?>