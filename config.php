<?php

class config {
    // accesses .env and its environment variables
    function loadEnv($path, $local) {
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            list($name, $value) = explode('=', $line, 2);
            putenv("$name=$value");
        }
    }

    // Somehow include if statement that changes db based on local or deployed
    public static $db = [
        "host" => "localhost",
        "port" => 5432,
        "user" => "user",
        "pass" => "",
        "database" => "example"
    ];
    public static $client = [
        "region" => getenv(""),
        "aws_access_key_id" => getenv(""),
        "aws_secret_access_key" => getenv(""),
    ];

    public static $bucket = [
        "bucket" => getenv(""),
    ]
}


?>