<?php
require_once('src/config/database.php');
require_once('src/models/attendance.php');

$database = new Database();
$db = $database->getConnection();
$attendance = new Attendance($db);

$studentID = filter_input(INPUT_POST, 'studentID', FILTER_VALIDATE_INT);
$courseID = filter_input(INPUT_POST, 'courseID', FILTER_SANITIZE_SPECIAL_CHARS);
$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

if ($attendance->recordAttendance($studentID, $courseID, $date, $status)) {
    header("Location: index.php?courseID=$courseID&message=Attendance recorded successfully");
} else {
    header("Location: index.php?courseID=$courseID&error=Failed to record attendance");
}
exit();

