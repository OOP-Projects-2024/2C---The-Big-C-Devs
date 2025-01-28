<?php
require_once('src/config/database.php');
require_once('src/models/post.php');

$database = new Database();
$db = $database->getConnection();
$post = new Post($db);

// Get the form data
$courseID = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_SPECIAL_CHARS);
$firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
$lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

// Insert the student into the database using the Post model
$post->addStudent($courseID, $firstName, $lastName, $email);

// Redirect back to the home page with the current course selection and the resulting students
header('Location: index.php?courseID=' . $courseID);

?>

