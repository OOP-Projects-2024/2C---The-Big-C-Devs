<?php
require_once('src/config/database.php');
require_once('src/models/assignment.php');

$database = new Database();
$db = $database->getConnection();
$assignment = new Assignment($db);

$courseID = filter_input(INPUT_POST, 'courseID', FILTER_SANITIZE_SPECIAL_CHARS);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$dueDate = filter_input(INPUT_POST, 'dueDate', FILTER_SANITIZE_STRING);

if ($assignment->createAssignment($courseID, $title, $description, $dueDate)) {
    header("Location: index.php?courseID=$courseID&message=Assignment added successfully");
} else {
    header("Location: add_assignment_form.php?courseID=$courseID&error=Failed to add assignment");
}
exit();

