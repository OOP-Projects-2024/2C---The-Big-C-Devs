<?php
require_once('src/config/database.php');
require_once('src/models/get.php');

$database = new Database();
$db = $database->getConnection();
$get = new Get($db);

$courseID = filter_input(INPUT_GET, 'courseID', FILTER_SANITIZE_SPECIAL_CHARS);
$course = $get->getCourseById($courseID);
?>


