<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo json_encode([
    'status' => 'success',
    'message' => 'API is working',
    'server' => $_SERVER['SERVER_SOFTWARE'],
    'php_version' => PHP_VERSION
]);

