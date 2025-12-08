<?php 
// Created this so my messy code doesn't get in the way of other
// development! Feel free to integrate later. 
require __DIR__ . '/vendor/autoload.php';

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
    public function upload($key, $source_path) {
        // $file_name = $source_path.uniqid();
        $url = $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'SourceFile' => $source_path,
        ]);
        $objectUrl = isset($url['ObjectURL']) ? (string)$url['ObjectURL'] : '';
        return [$key, $objectUrl];
    }

    // Create a short-lived URL for browser download (private bucket friendly)
    public function presignGetUrl($key, $expires = '+5 minutes') {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key'    => $key,
        ]);
        $request = $this->client->createPresignedRequest($cmd, $expires);
        return (string)$request->getUri();
    }

    public function download($key, $name) {
        $file = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
        header('Content-Type: ' . $file['ContentType']);
        header('Content-Disposition: attachment; filename="' . $name . '"');
        echo $file['Body'];
    }

    // You can also delete multiple objects with deleteObjects() <- maybe use this by default?
    // Should be able to delete a specific resource
    public function delete($key) {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
    }

    public function downloadMultiple($zip_name, $keys) {
        $zip = new ZipArchive();
        $zip->open($zip_name, ZipArchive::CREATE);

        foreach ($keys as $key) {
            $file = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            $zip->addFromString(basename($key), $file['Body']);
        }

        $zip->close();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_name . '"');
        header('Content-Length: ' . filesize($zip_name));
        readfile($zip_name);

        unlink($zip_name);
        exit;
    }

}
?>