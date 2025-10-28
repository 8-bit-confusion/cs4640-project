<?php 
// Created this so my messy code doesn't get in the way of other
// development! Feel free to integrate later. 
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
class BucketController {
    private $client;
    private $bucket;
    public function __construct() {
        $this->bucket = config::$bucket["bucket"];

        $region = config::$client["region"];
        $access_key = config::$client["aws_access_key_id"];
        $secret_key = config::$client["aws_secret_access_key"];
        $this->client = new S3Client(['region' => $region,
            'version' => 'latest',
            'credentials' => [
                'key' => $access_key,
                'secret' => $secret_key
            ]
        ]);
    }


}
?>