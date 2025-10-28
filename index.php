<?php

// DEBUG: print errors
// (from trivia example)
error_reporting(E_ALL);
ini_set("display_errors", 1);

// autoload classes from src/
// OUR AnagramsGameController.php FILE WILL BE DEPLOYED IN
// OPT/SRC, NOT IN A PUBLIC FOLDER
// (from trivia example)
spl_autoload_register(function ($classname) {
    include "$classname.php";
});

// instantiate and run controller
// (from trivia example)
$controller = new SessionController();
$controller->run();

?>