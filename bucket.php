<?php 
// Created this so my messy code doesn't get in the way of other
// development! Feel free to integrate later. 
use Aws\S3\S3Client;

$client = new S3Client(['region' => 'us-east-2']);
$results = $client->listBuckets();
var_dump($results);

$this->bucketName = "f25-cs4640"



?>