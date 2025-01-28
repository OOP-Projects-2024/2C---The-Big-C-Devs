<?php
require_once('src/config/database.php');
require_once('src/models/get.php');
require_once('src/models/grade.php');
require_once('src/models/attendance.php');

$database = new Database();
$db = $database->getConnection();
$get = new Get($db);
$grade = new Grade($db);
$attendance = new Attendance($db);

$studentID = filter_input(INPUT_GET, 'studentID', FILTER_VALIDATE_INT);
$student = $get->getStudentById($studentID);

if (!$student) {
    header("Location: index.php?error=Student not found");
    exit();
}

$courseID = $student['courseID'];
$course = $get->getCourseById($courseID);
$grades = $grade->getGradesByStudent($studentID, $courseID);
$attendanceRecords = $attendance->getAttendanceByStudent($studentID, $courseID);
?>


