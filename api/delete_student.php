<?php
require_once('src/config/database.php');
require_once('src/models/delete.php');

$database = new Database();
$db = $database->getConnection();
$delete = new Delete($db);

// Get the studentID to delete
$studentID = filter_input(INPUT_POST, 'studentID', FILTER_VALIDATE_INT);

if ($studentID) {
    // Delete the student from the database
    $delete->deleteStudent($studentID);
}

// Redirect back to the current course's student list page
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: index.php'); // Redirect to index if HTTP_REFERER is not available
}

?>

