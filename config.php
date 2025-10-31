<?php

class Config {
    // accesses .env and its environment variables
    private $dotenv = [];

    public function __construct($path) {
        if (!file_exists($path)) {
            echo "ENV file not found";
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            list($name, $value) = explode(' = ', $line, 2);
            $this->dotenv[$name] = $value;
        }
    }

    // Somehow include if statement that changes db based on local or deployed
    public function local_db() {
        return [
            "host" => "db",
            "port" => 5432,
            "user" => "localuser",
            "pass" => $this->dotenv["DATABASE_LOCAL_PASSWORD"],
            "database" => "example"
        ];
    }

    public function server_db() {
        return [
            "host" => "localhost",
            "port" => 5432,
            "user" => $this->dotenv["DATABASE_SERVER_USERNAME"],
            "pass" => $this->dotenv["DATABASE_SERVER_PASSWORD"],
            "database" => $this->dotenv["DATABASE_SERVER_USERNAME"]
        ];
    }
    
    public function client () {
        return [
            "region" => $this->dotenv["AWS_REGION"],
            "aws_access_key_id" => $this->dotenv["AWS_ACCESS_KEY_ID"],
            "aws_secret_access_key" => $this->dotenv["AWS_SECRET_ACCESS_KEY"],
        ];
    }

    public function bucket() {
        return [
            "bucket" => $this->dotenv["S3_BUCKET"],
        ];
    }
}


?>