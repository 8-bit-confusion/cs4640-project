<?php 
// Created this so my messy code doesn't get in the way of other
// development! Feel free to integrate later. 
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
class Bucket{
    private $client;
    private $bucket;
    public function __construct($config) {
        $this->bucket = $config->bucket()["bucket"];

        $region = $config->client()["region"];
        $access_key = $config->client()["aws_access_key_id"];
        $secret_key = $config->client()["aws_secret_access_key"];
        $this->client = new S3Client(['region' => $region,
            'version' => 'latest',
            'credentials' => [
                'key' => $access_key,
                'secret' => $secret_key
            ]
        ]);
    }

    // Returns the unique key and the AWS path
    public function upload($source_path) {
        $file_name = $source_path.uniqid();
        $url = $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $file_name,
            'SourceFile' => $source_path,
        ]);
        return [$file_name, $url];
    }

    public function download($key) {
        $file = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
        $body = $file->get('Body');
        $body->rewind();
    }

    // You can also delete multiple objects with deleteObjects() <- maybe use this by default?
    // Should be able to delete a specific resource
    public function delete($key) {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
    }

}
?>