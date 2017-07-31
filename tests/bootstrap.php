<?php
include 'vendor/autoload.php';
global $configRoot;
global $databaseConfig;

$databaseConfig = array(
    "driver" => "pdo_mysql",
    "user" => "testuser",
    "password" => "testpassword",
    "host" => "localhost",
    "dbname" => "testdb"
);
$configRoot = __DIR__;