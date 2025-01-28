<?php
require_once('src/config/database.php');
require_once('src/models/post.php');
require_once('src/models/get.php');

$database = new Database();
$db = $database->getConnection();
$post = new Post($db);
$get = new Get($db);

// Get course data from form
$course_id = filter_input(INPUT_POST, 'course_id');
$course_name = filter_input(INPUT_POST, 'course_name');

// Validate input
if (empty($course_id) || empty($course_name)) {
    // Redirect back to the add_course_form.php with an error message
    header("Location: add_course_form.php?error=Invalid input. Please fill in all fields.");
    exit();
} else {
    // Check if the course already exists
    if ($get->getCourseById($course_id)) {
        // Redirect back to the add_course_form.php with an error message
        header("Location: add_course_form.php?error=Course ID already exists. Please choose a different ID.");
        exit();
    }

    // Insert the course into the database using the Post model
    $post->addCourse($course_id, $course_name);
}

// Redirect to the index.php page of the newly added course
header("Location: index.php?courseID=$course_id");
exit();

