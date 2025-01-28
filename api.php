<?php
require_once('src/config/database.php');
require_once('src/models/get.php');
require_once('src/models/post.php');
require_once('src/models/patch.php');
require_once('src/models/delete.php');
require_once('src/models/assignment.php');
require_once('src/models/attendance.php');
require_once('src/models/grade.php');

header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

$get = new Get($db);
$post = new Post($db);
$patch = new Patch($db);
$delete = new Delete($db);
$assignment = new Assignment($db);
$attendance = new Attendance($db);
$grade = new Grade($db);

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

switch($request[0]) {
    case 'courses':
        switch($method) {
            case 'GET':
                if(isset($request[1])) {
                    $result = $get->getCourseById($request[1]);
                } else {
                    $result = $get->getAllCourses();
                }
                sendResponse($result);
                break;
            case 'POST':
                if(!isset($input['courseID']) || !isset($input['courseName'])) {
                    sendResponse(["error" => "Missing required fields"], 400);
                }
                $result = $post->addCourse($input['courseID'], $input['courseName']);
                sendResponse(["success" => $result], $result ? 201 : 400);
                break;
            case 'PATCH':
                if(!isset($request[1]) || !isset($input['courseName'])) {
                    sendResponse(["error" => "Missing required fields"], 400);
                }
                $result = $patch->updateCourse($request[1], $input['courseName']);
                sendResponse(["success" => $result]);
                break;
            case 'DELETE':
                if(!isset($request[1])) {
                    sendResponse(["error" => "Course ID is required"], 400);
                }
                $result = $delete->deleteCourse($request[1]);
                sendResponse(["success" => $result]);
                break;
        }
        break;

    case 'students':
        switch($method) {
            case 'GET':
                if(isset($request[1])) {
                    $result = $get->getStudentById($request[1]);
                } elseif(isset($_GET['courseID'])) {
                    $result = $get->getStudentsByCourse($_GET['courseID']);
                } else {
                    $result = $get->getAllStudents();
                }
                sendResponse($result);
                break;
            case 'POST':
                if(!isset($input['courseID']) || !isset($input['firstName']) || !isset($input['lastName']) || !isset($input['email'])) {
                    sendResponse(["error" => "Missing required fields"], 400);
                }
                $result = $post->addStudent($input['courseID'], $input['firstName'], $input['lastName'], $input['email']);
                sendResponse(["success" => $result], $result ? 201 : 400);
                break;
            case 'PATCH':
                if(!isset($request[1]) || !isset($input['courseID']) || !isset($input['firstName']) || !isset($input['lastName']) || !isset($input['email'])) {
                    sendResponse(["error" => "Missing required fields"], 400);
                }
                $result = $patch->updateStudent($request[1], $input['courseID'], $input['firstName'], $input['lastName'], $input['email']);
                sendResponse(["success" => $result]);
                break;
            case 'DELETE':
                if(!isset($request[1])) {
                    sendResponse(["error" => "Student ID is required"], 400);
                }
                $result = $delete->deleteStudent($request[1]);
                sendResponse(["success" => $result]);
                break;
        }
        break;

    case 'assignments':
        switch($method) {
            case 'GET':
                if(isset($request[1])) {
                    // Implement getAssignmentById in the Assignment class if needed
                    $result = $assignment->getAssignmentById($request[1]);
                } elseif(isset($_GET['courseID'])) {
                    $result = $assignment->getAssignmentsByCourse($_GET['courseID']);
                } else {
                    sendResponse(["error" => "Course ID is required"], 400);
                }
                sendResponse($result);
                break;
            case 'POST':
                if(!isset($input['courseID']) || !isset($input['title']) || !isset($input['description']) || !isset($input['dueDate'])) {
                    sendResponse(["error" => "Missing required fields"], 400);
                }
                $result = $assignment->createAssignment($input['courseID'], $input['title'], $input['description'], $input['dueDate']);
                sendResponse(["success" => $result], $result ? 201 : 400);
                break;
            case 'PATCH':
                if(!isset($request[1]) || !isset($input['title']) || !isset($input['description']) || !isset($input['dueDate'])) {
                    sendResponse(["error" => "Missing required fields"], 400);
                }
                $result = $assignment->updateAssignment($request[1], $input['title'], $input['description'], $input['dueDate']);
                sendResponse(["success" => $result]);
                break;
            case 'DELETE':
                if(!isset($request[1])) {
                    sendResponse(["error" => "Assignment ID is required"], 400);
                }
                $result = $assignment->deleteAssignment($request[1]);
                sendResponse(["success" => $result]);
                break;
        }
        break;

    case 'attendance':
        switch($method) {
            case 'GET':
                if(isset($_GET['studentID']) && isset($_GET['courseID'])) {
                    $result = $attendance->getAttendanceByStudent($_GET['studentID'], $_GET['courseID']);
                } elseif(isset($_GET['courseID']) && isset($_GET['date'])) {
                    $result = $attendance->getAttendanceByCourse($_GET['courseID'], $_GET['date']);
                } else {
                    sendResponse(["error" => "Missing required parameters"], 400);
                }
                sendResponse($result);
                break;
            case 'POST':
                if(!isset($input['studentID']) || !isset($input['courseID']) || !isset($input['date']) || !isset($input['status'])) {
                    sendResponse(["error" => "Missing required fields"], 400);
                }
                $result = $attendance->recordAttendance($input['studentID'], $input['courseID'], $input['date'], $input['status']);
                sendResponse(["success" => $result], $result ? 201 : 400);
                break;
            // PATCH and DELETE methods for attendance are not implemented in this example
        }
        break;

    case 'grades':
        switch($method) {
            case 'GET':
                if(isset($_GET['studentID']) && isset($_GET['courseID'])) {
                    $result = $grade->getGradesByStudent($_GET['studentID'], $_GET['courseID']);
                } elseif(isset($_GET['assignmentID'])) {
                    $result = $grade->getGradesByAssignment($_GET['assignmentID']);
                } else {
                    sendResponse(["error" => "Missing required parameters"], 400);
                }
                sendResponse($result);
                break;
            case 'POST':
                if(!isset($input['studentID']) || !isset($input['assignmentID']) || !isset($input['score'])) {
                    sendResponse(["error" => "Missing required fields"], 400);
                }
                $result = $grade->addGrade($input['studentID'], $input['assignmentID'], $input['score']);
                sendResponse(["success" => $result], $result ? 201 : 400);
                break;
            // PATCH and DELETE methods for grades are not implemented in this example
        }
        break;

    default:
        sendResponse(["error" => "Invalid endpoint"], 404);
        break;
}

